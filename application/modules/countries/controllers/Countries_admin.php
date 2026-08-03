<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Countries_admin extends Base_Admin_Controller {

    var $search_term;

    function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->model('countries/countries_model');

        $this->breadcrumb->add(lang('country-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'countries');
        // Login check for admin
        $this->access_control($this->access_rules());
        $this->language = $this->uri->segment(4);

        $this->user_id = isset($this->session->userdata[$this->section_name]['user_id'])?$this->session->userdata[$this->section_name]['user_id']:0;
        $this->countries_model->user_id = $this->user_id;
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {
        return array(
            array(
                'actions' => array('index','edit','delete','view_data'),
                'users' => array('@'),
            )
        );
    }

    function index() {
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->countries_model->record_per_page = $this->record_per_page;
        $this->countries_model->offset = $offset;


        //set sort/search parameters in pagging
        if ($this->input->post()) {

            $data = $this->input->post();
            // Search Term ***
            if (isset($data['search_term']) && !empty($data['search_term'])) {
                $this->countries_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "country_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "country_search_term", "");
            }
            // Search Term ***
            // Sort Order ***
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->countries_model->sort_by = $data['sort_by'];
                $this->countries_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "country_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "country_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "country_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "country_sort_order", "");
            }
            // Sort Order ***


            if (isset($data['type']) && $data['type'] == 'delete') {

                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = $val;
                }
                // Newly added
                if ($this->countries_model->delete_records($tempArr)) {
                    echo $this->theme->message(lang('country-delete-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('country-contains-site-error'), 'error');
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
                if ($this->countries_model->active_records($tempArr)) {
                    echo $this->theme->message(lang('country-active-success'), 'success');
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
                if ($this->countries_model->inactive_records($tempArr)) {
                    echo $this->theme->message(lang('country-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('country-contains-site-error'), 'error');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->countries_model->active_all_records()) {
                    echo $this->theme->message(lang('country-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->countries_model->inactive_all_records()) {
                    echo $this->theme->message(lang('country-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('country-contains-site-error'), 'error');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['country_search_term'])) {
            $this->countries_model->search_term = trim($this->session->userdata[$this->section_name]['country_search_term']);
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['country_sort_by'])) {
            $this->countries_model->sort_by = $this->session->userdata[$this->section_name]['country_sort_by'];
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['country_sort_order'])) {
            $this->countries_model->sort_order = $this->session->userdata[$this->section_name]['country_sort_order'];
        }


        //Load data for url listing
        $countries = $this->countries_model->getCountries();
        $this->countries_model->_record_count = true;
        $total_records = $this->countries_model->getCountries();
        // Pass data to view file
        $this->search_term = $this->countries_model->search_term;
        $data['countries'] = $countries;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->countries_model->search_term;
        $data['sort_by'] = $this->countries_model->sort_by;
        $data['sort_order'] = $this->countries_model->sort_order;

        //Create page-title
        $this->theme->set('page_title', lang('country-management'));

        //Render view
        $this->theme->view($data);
    }

    public function check_country_edit_unique($str, $field) {
        list($table,$field,$field1,$value1) = explode('.', $field);

        if(!empty($value1)){
            $this->db->where($field1.' !=',$value1);
        }
        $this->db->where('status !=', -1);
        $query = $this->db->limit(1)->get_where($table, array($field => $str));
        
        $this->form_validation->set_message('check_country_edit_unique', lang('msg-country-name-already-exists'));
        return $query->num_rows() === 0;
    }

    function edit($id=0){
        if($this->input->post('mysubmit') == '1'){
            $checkid = $this->input->post('id');
            $this->form_validation->set_rules('country', lang('country'), 'trim|required|callback_check_country_edit_unique['.TBL_COUNTRIES.'.country.id.'.$checkid.']');
            if ($this->form_validation->run($this)) {
                $data['id']         = $this->input->post('id');
                $data['country']    = $this->input->post('country');
                $data['status']     = $this->input->post('status');
                $data['site_id']    = $this->session->userdata[$this->section_name]['site_id'];

                $id = $this->countries_model->saveCountry($data);

                $this->theme->set_message(lang('country-save-success'), 'success');
                redirect(BASE_ADMIN_URL_CUSTOM.'countries');
            }
        }

        $country = $this->countries_model->getCountry($id);
        if(!empty($country)){
            $data['id']         = $country['id'];
            $data['country']    = $country['country'];
            $data['status']     = $country['status'];
            $this->breadcrumb->add(lang('edit-country'), base_url() . BASE_ADMIN_URL_CUSTOM . 'countries');
        }else{
            $this->breadcrumb->add(lang('add-country'), base_url() . BASE_ADMIN_URL_CUSTOM . 'countries');
        }

        $this->theme->view($data);
    }

    function delete() {
        $data = $this->input->post();
        $id = intval(base64_decode($data['id']));

        $result = $this->countries_model->getCountry($id);
        if (!empty($result)) {
            $res = $this->countries_model->deleteCountry($id);
            if ($res) {
                echo $this->theme->message(lang('country-delete-success'), 'success');
            }
        } else {
            echo $this->theme->message(lang('invalid-id-msg'), 'error');
        }
    }

    public function view_data($id = 0) {
        $country = $this->countries_model->getCountry($id);

        if(empty($country)){
            show_404();
        }

        $data['country'] = $country['country'];
        $data['status'] = $country['status'];

        $this->breadcrumb->add(lang('view-country'));
        $this->theme->view($data);
    }

}
