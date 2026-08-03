<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 *  User login log Admin Controller
 *
 *  User login log Admin controller to display add / Edit / Delete / List User login log page for each language.
 *
 * @package CIDemoApplication
 *
 * @copyright    (c) 2013, TatvaSoft
 * @author AMPT
 */
class Audit_trail_admin extends Base_Admin_Controller
{
    /*
     * Create an instance
     */

    public function __construct()
    {
        parent::__construct();
        // Login check for admin
        $this->access_control($this->access_rules());
        $this->language = $this->uri->segment(4);
        $this->load->library('unit_test');

        $this->load->model('audit_trail_model');

    }

    /**
     * function accessRules to check page access
     */
    private function access_rules()
    {
        return array(
            array(
                'actions' => array('index', 'delete'),
                'users'   => array('@'),
            ),
        );
    }

    /**
     * action to display language wise list of cms page
     * @param string $language_code
     */
    public function index($language_code = '')
    {
        //Paging parameters
        $offset                                   = get_offset($this->page_number, $this->record_per_page);
        $this->audit_trail_model->record_per_page = $this->record_per_page;
        $this->audit_trail_model->offset          = $offset;

        $data = $this->input->post();

        $filters = array();
        if (!empty($data['search_startdate'])) {
            $date                  = new DateTime($data['search_startdate']);
            $filters['start_date'] = $date->format('Y-m-d');
        }
        if (!empty($data['search_enddate'])) {
            $date                = new DateTime($data['search_enddate']);
            $filters['end_date'] = $date->format('Y-m-d');
        }
        if (!empty($data['role_select'])) {
            $filters['role_id'] = $data['role_select'];
        }
        if (!empty($data['sort_by'])) {
            $filters['sort_by'] = $data['sort_by'];
        }
        if (!empty($data['sort_order'])) {
            $filters['sort_order'] = $data['sort_order'];
        }        
        // if (!empty($data['site_id'])) {
        //     $filters['site_id'] = $data['site_id'];
        // }
       
        if (!empty($data['search_site'])) {
            $filters['search_site'] = $data['search_site'];
        }

        if (!empty($data['search_term_module'])) {

            $filters['search_term_module'] = $data['search_term_module'];

        }

        // pre($data);
        if (empty($data['page_number'])) {
            $this->session->set_custom_userdata($this->section_name, "user_offset", "");
            $this->session->set_custom_userdata($this->section_name, "user_record_per_page", "");
        }

        if (!empty($this->session->userdata[$this->section_name]['user_offset'])) {
            $this->audit_trail_model->offset = $this->session->userdata[$this->section_name]['user_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['user_record_per_page'])) {
            $this->audit_trail_model->page_number = $this->session->userdata[$this->section_name]['user_record_per_page'];
        }
        if (isset($data['search_site']) && !empty($data['search_site'])) {
            // $this->audit_trail_model->search_site = trim($data['search_site']);
            $this->session->set_custom_userdata($this->section_name, "search_site", $this->input->post('search_site'));
        } else {
            // $this->audit_trail_model->search_site = '';
            $this->session->set_custom_userdata($this->section_name, "search_site", "");
        } 

        if (!empty($this->session->userdata[$this->section_name]['search_site'])) {
            $this->audit_trail_model->search_site = trim($this->session->userdata[$this->section_name]['search_site']);
        }

        if (isset($data['search_term_module']) && !empty($data['search_term_module'])) {

            $this->session->set_custom_userdata($this->section_name, "search_term_module", $this->input->post('search_term_module'));

        } else {

            $this->session->set_custom_userdata($this->section_name, "search_term_module", "");

        } 



        if (!empty($this->session->userdata[$this->section_name]['search_term_module'])) {

            $this->audit_trail_model->search_term_module = trim($this->session->userdata[$this->section_name]['search_term_module']);

        }



        $logs                  = $this->audit_trail_model->getLog($filters);
        $total_records         = $this->audit_trail_model->getLogCount($filters);
        $data['csrf_token']    = $this->security->get_csrf_token_name();
        $data['csrf_hash']     = $this->security->get_csrf_hash();
        $data['logs']          = $logs;
        $data['roles']         = $this->audit_trail_model->getRoles();
        $data['total_records'] = $total_records;
        $data['page_number']   = $this->page_number;
        $this->load->model('reports/reports_model');
        $this->load->model('projects/projects_model');
        $this->load->model('sites/sites_model');
        $data['hotel_sites'] = $this->sites_model->get_login_sites();
        $data['search_site'] = $this->audit_trail_model->search_site;
        $data['search_term_module'] = $this->audit_trail_model->search_term_module;

        // Breadcrumb settings
        $this->breadcrumb->add('Audit Log', base_url() . $this->section_name . '/audit_trail');
        //Create page-title
        $this->theme->set('page_title', 'Audit Trail');

        //Render view
        $this->theme->view($data); 
    }
}
