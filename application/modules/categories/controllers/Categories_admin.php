<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Categories Admin Controller
 *
 *  Categories Admin controller to display Add / Edit / Delete / List Category page for each language.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author HTDO
 */
class Categories_admin extends Base_Admin_Controller
{
    /*
     * Create an instance
     */
    function __construct()
    {
        parent::__construct();
        // Login check for admin
        $this->access_control($this->access_rules());

        // Load required helpers
        $this->load->helper('url');
        $this->load->helper('ckeditor');

        // load required models
        $this->load->model('cms/cms_meta_model');
        $this->load->model('urls/urls_model');
        $this->load->model('categories/categories_model');

        // Breadcrumb settings
        $this->breadcrumb->add(lang('category_management'), base_url().$this->section_name. '/categories');
    }

    /**
     * function accessRules to check page access
     */
    private function access_rules()
    {
        return array(
            array(
                'actions' => array('action', 'index', 'ajax_index', 'ajax_action', 'delete','get_module_wish_category','view','ajax_view'),
                'users' => array('@'),
            )/*,
            array(
                'actions' => array('index'),
                'users' => array('*'),
            )*/
        );
    }

    /**
     * action to display language wise list of categories page
     * @param string $language_code
     */
    /*function index($language_code = '')
    {
        //Type Casting
        $language_code = strip_tags($language_code);

        //Initialize
        if ($language_code == '')
        {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //Logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);

        $language_id = $language_detail[0]['l']['id'];
        $language_list = $this->languages_model->get_languages();

        //Set page title
        $this->theme->set('page_title', lang('category_list'));

        // Breadcrumb settings
        // No breadcrumb as it's index page
        //Variable assignments to view
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_id'] = $language_id;
        $data['languages_list'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $this->theme->view($data);
    }*/

    /**
     * action to load list of categories based on language passed or from default language
     * @param string $language_code
     */
    function index()
    {
        if($this->input->post('page_number') != "")
        {
            $this->page_number = $this->input->post('page_number');
        }

        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->categories_model->record_per_page = $this->record_per_page;
        $this->categories_model->offset = $offset;

        //set sort/search parameters in pagging
        if ($this->input->post())
        {
            $data = $this->input->post();

            if (isset($data['search_term']))
            {
                $this->categories_model->search_term = $data['search_term'];
                $this->session->set_custom_userdata($this->section_name, "search_term",$this->categories_model->search_term);
            }
            if (isset($data['sort_by']) && $data['sort_order'])
            {
                $this->categories_model->sort_by = $data['sort_by'];
                $this->categories_model->sort_order = $data['sort_order'];
            }
            if(isset($data['type']) && $data['type']=='delete')
            {
                if($this->categories_model->delete_records($data['ids']))
                {
                     echo $this->theme->message(lang('delete_success'), 'success');exit;
                }
            }
            if(isset($data['type']) && $data['type']=='active')
            {
                if($this->categories_model->active_records($data['ids']))
                {
                    echo $this->theme->message(lang('active-success'), 'success');exit;
                }
            }
            if(isset($data['type']) && $data['type']=='inactive')
            {
                if($this->categories_model->inactive_records($data['ids']))
                {
                    echo $this->theme->message(lang('inactive-success'), 'success');exit;
                }
            }
            if(isset($data['type']) && $data['type']=='active_all')
            {
                if($this->categories_model->active_all_records())
                {
                    echo $this->theme->message(lang('active-success'), 'success');exit;
                }
            }
            if(isset($data['type']) && $data['type']=='inactive_all')
            {
                if($this->categories_model->inactive_all_records())
                {
                    echo $this->theme->message(lang('inactive-success'), 'success');exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['search_term'])) {
            $this->categories_model->search_term = $this->session->userdata[$this->section_name]['search_term'];
        }
        
        $category_list = $this->categories_model->get_category_listing();
        $this->categories_model->_record_count = true;
        $total_records = $this->categories_model->get_category_listing();

        //Variable assignments to view
        $data = array(
            'category_list' => $category_list,
            'page_number' => $this->page_number,
            'total_records' => $total_records,
            'search_term' => $this->categories_model->search_term,
            'sort_by' => $this->categories_model->sort_by,
            'sort_order' => $this->categories_model->sort_order
        );
        $this->theme->view($data);
    }

     /**
     * action view to view categories page
     * @param string $language_code
     * @param string $id : if in edit mode
     */
    function view($language_code = '', $id = 0)
    {
        //Type Casting
        $language_code = strip_tags($language_code);
        $id = intval($id);

        //Initialize
        $category_list = array();
        $category_list_result = array();
        if ($language_code == '')
        {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        // Logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if (isset($id) && $id != '' && $id != '0')
        {
            $category_list_result = $this->categories_model->get_category_view_detail_by_id($id, $language_detail[0]['l']['id']);
            $category_list = array_merge($category_list_result[0]['c'], $category_list_result[0]['cm']);
        }
        $language_list = $this->languages_model->get_languages(); // get list of languages
        // Breadcrumb settings

        $this->theme->set('page_title', lang('view_categories'));
        $this->breadcrumb->add(lang('view_categories'));

        //Variable assignments to view
        $data = array();
        $data['id'] = $id;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['category'] = $category_list;
        $data['content'] = $this->load->view('admin_ajax_view', $data, TRUE);
        $this->theme->view($data);
    }

    /**
     * action to add/edit categories page load form from ajax based on language
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     * @param string $ajax_load : will be 1 if load from ajax mode
     */
    function ajax_view($language_code = '', $id = 0, $ajax_load = 1)
    {
        //Type Casting
        $language_code = strip_tags($language_code);
        $id = intval($id);
        $ajax_load = intval($ajax_load);

        //Initialize
        $category_list = array();
        $category_list_result = array();
        $data = array();
        if ($language_code == '')
        {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        if (isset($id) && $id != '' && $id != '0')
        {
            $category_list_result = $this->categories_model->get_category_view_detail_by_id($id, $language_detail[0]['l']['id']);
            if (!empty($category_list_result))
            {
                $category_list = array_merge($category_list_result[0]['c'], $category_list_result[0]['cm']);
            }
        }
        //Variable assignments to view
        $data['id'] = $id;
        $data['language_id'] = $language_detail[0]['l']['id'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_code'] = $language_code;
        $data['categories'] = $category_list;
        if ($ajax_load == '1')
            echo $this->load->view('admin_ajax_view', $data);
        else
            return $this->load->view('admin_ajax_view', $data);
    }

     /**
     * action to add/edit category page
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     */
    function action($action, $language_code = '', $id = 0)
    {
        //Type Casting
        $action = trim(strip_tags($action));
        $language_code = strip_tags($language_code);
        $id = intval($id);

        if ($this->input->post('categorysubmit'))
        {
            $parent_id = intval($this->input->post('parent_id'));
            $module_id = intval($this->input->post('module_id'));
            $title = trim(strip_tags($this->input->post('title')));
            $slug_url = trim(strip_tags($this->input->post('slug_url')));
            $description = trim($this->input->post('description'));
            $status = trim(strip_tags($this->input->post('status')));
        }

        if ($action == 'add' || $action == 'edit')
        {
            //Initialize
            $category_list = array();
            $category_list_result = array();
            if ($language_code == '')
            {
                $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
            }

            // Logic
            $language_detail = $this->languages_model->get_languages_by_code($language_code);
            $language_id = $language_detail[0]['l']['id'];
            if ($this->input->post('categorysubmit'))
            {
                //Validation Check
                $this->load->library('form_validation');
                $this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]');
                // $this->form_validation->set_rules('slug_url', lang('slug_url'), 'required|callback_check_unique_slug_url');

                if ($this->form_validation->run($this) == true)
                {
                    $category_data = $this->categories_model->get_related_lang_category($id, $language_id, $module_id);

                    $this->categories_model->parent_id = $parent_id;
                    $this->categories_model->module_id = $module_id;
                    $this->categories_model->title = $title;
                    $this->categories_model->slug_url = $slug_url;
                    $this->categories_model->description = $description;
                    $this->categories_model->status = $status;

                    if (count($category_data) > 0)
                    {
                        $this->categories_model->update_category($id, $language_id);

                        if($status == 0)
                        {
                            $this->categories_model->inactive_all_child_category($id, $language_id, $module_id);
                        }

                        $this->theme->set_message(lang('msg_update_success'), 'success');
                        $data_action = 'Update';
                    }
                    else
                    {
                        if ($id == '0' || $id == '')
                        {
                            $last_category_id = $this->categories_model->get_last_category_id();
                            $id = $last_category_id + 1;
                        }
                        $this->categories_model->insert_category($id, $language_id);
                        $this->theme->set_message(lang('msg_add_success'), 'success');
                        $data_action = 'Create';
                    }
                    // Save audit trail
                    $site_id = $this->session->userdata[$this->section_name]['site_id'];
                    $user_id = $this->session->userdata[$this->section_name]['user_id'];
                    saveAuditTrail($user_id, $site_id, 'Forums (Category)', $data_action);

                    // $this->config_model->change_slug_url_management($this->input->post('old_slug_url'), $this->input->post('slug_url'), 'categories', $id, 'index/' . $this->input->post('slug_url'), $this->input->post('status'));

                    //$this->load->module('urls/urls_admin');
                    //$this->urls_admin->generate_custom_url();

                    //redirect(BASE_ADMIN_URL_CUSTOM.'categories/index/' . $language_detail[0]['l']['language_code']);
                    redirect(BASE_ADMIN_URL_CUSTOM.'categories/index');
                }
            }

            if (!$this->input->post())
            {
                if (isset($id) && $id != '' && $id != '0')
                {
                    $category_list_result = $this->categories_model->get_category_detail_by_id($id, $language_detail[0]['l']['id']);
                    $category_list = $category_list_result[0]['c'];
                }
            }
            else
            {
                $category_list = $this->input->post();
            }
            $language_list = $this->languages_model->get_languages(); // get list of languages
            // Breadcrumb settings
            if ($action == "add")
            {
                $this->theme->set('page_title', $this->lang->line('add_category'));
                $this->breadcrumb->add($this->lang->line('add_category'));
                $id = '';
            }
            elseif ($action == "edit")
            {
                $this->theme->set('page_title', $this->lang->line('edit_category'));
                $this->breadcrumb->add($this->lang->line('edit_category'));
            }

            //Variable assignments to view
            $data = array();
            $data['action'] = $action;
            $data['id']     = $id;
            $data['language_code'] = $language_detail[0]['l']['language_code'];
            $data['language_name'] = $language_detail[0]['l']['language_name'];
            $data['language_id']   = $language_id;
            $data['csrf_token']    = $this->security->get_csrf_token_name();
            $data['csrf_hash']     = $this->security->get_csrf_hash();
            $data['languages']     = $language_list;
            $data['category']      = $category_list;

            //Category Modulelist
            $category_module_list = $this->categories_model->get_category_module_list();
            $data['category_module_list'] = $category_module_list;

            $data['content'] = $this->load->view('admin_ajax_action', $data, TRUE);
            $this->theme->view($data, 'admin_action');
        }
        else
        {
            $this->theme->set_message(lang('permission_not_allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM.'categories');
            exit;
        }
    }

    /**
     * action to add/edit category page load form from ajax based on language
     * @param string $action : add or edit
     * @param string $language_code
     * @param string $id : if in edit mode
     * @param string $ajax_load : will be 1 if load from ajax mode
     */
    function ajax_action($action, $language_code = '', $id = 0, $ajax_load = 1)
    {
        //Type Casting
        $action = trim(strip_tags($action));
        $language_code = strip_tags($language_code);
        $id = intval($id);
        $ajax_load = intval($ajax_load);

        //Initialize
        $category_list = array();
        $category_list_result = array();
        $data = array();
        if ($language_code == '')
        {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        //logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);

        if (isset($id) && $id != '' && $id != '0')
        {
            $category_list_result = $this->categories_model->get_category_detail_by_id($id, $language_detail[0]['l']['id']);
            if (!empty($category_list_result))
            {
                if (!empty($category_list_result[0]['c']))
                    $category_list = $category_list_result[0]['c'];
            }
        }
        //Variable assignments to view
        $data['action'] = $action;
        $data['id'] = $id;
        $data['language_id'] = $language_detail[0]['l']['id'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_code'] = $language_code;
        $data['category'] = $category_list;

        //Category Modulelist
        $category_module_list = $this->categories_model->get_category_module_list();
        $data['category_module_list'] = $category_module_list;

        if ($ajax_load == '1')
            echo $this->load->view('admin_ajax_action', $data);
        else
            return $this->load->view('admin_ajax_action', $data);
    }

    // Function to delete the category record and related url management record
    function delete()
    {
        //Initialise
        $id = $this->input->post('id');
        $slug_url = $this->input->post('slug_url');

        //Type casting
        $id = intval($id);
        $slug_url = trim(strip_tags($slug_url));

        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['category'] = $this->categories_model->delete_category($id);
            if ($slug_url != '')
            {
                $this->urls_model->delete_url_by_slug($slug_url);
            }
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    /**
     * function check_unique_slug_url to check unique slug url
     */
    public function check_unique_slug_url()
    {
        //variable assignement
        $id = '';

        //Get url management id
        if ($this->input->post('old_slug_url') != '')
        {
            $url_detail = $this->urls_model->get_url_management_id_by_slug($this->input->post('old_slug_url'));
            $id = $url_detail[0]['um']['id'];
        }

        $slug_url = $this->input->post('slug_url');
        $result = $this->urls_model->check_unique_slug($slug_url, $id);

        if (count($result) > 0)
        {
            $this->form_validation->set_message('check_unique_slug_url', lang('msg_alvailable_slug_url'));
            return false;
        }
        else
        {
            return true;
        }
    }

    function get_module_wish_category($language_code = '',$module_id, $value = 0)
    {
        $options = $temp_array = array();
        $options = array(0 => 'Root');

        $language_detail = $this->languages_model->get_languages_by_code($language_code);

        $this->categories_model->module_id =$module_id;
        $this->categories_model->first_option =lang('root');
        $options = $this->categories_model->get_category_with_child($language_detail[0]['l']['id']);

        echo form_dropdown('parent_id', $options,$value );
    }
}
