<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Utilities_admin extends Base_Admin_Controller
{

    public $fieldsArray = array();

    public function __construct()
    {
        parent::__construct();
        $this->access_control($this->access_rules());
        $this->load->model('utilities_model');
        $this->load->model('sites/site_waste_model');

        $this->utilities_model->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
        $this->utilities_model->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
        $this->utilities_model->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;

        $this->load->library('form_validation');
        $this->language = $this->uri->segment(4);

    }

    private function access_rules()
    {
        return array(
            array(
                'actions' => array('index', 'daily','quarterly','delete_action_image','delete_action','delete_ngo', 'delete_waste_image', 'delete_utility_image', 'delete_biodiversity', 'delete_biodiversity_image', 'waste','export_waste'),
                'users'   => array('@'),
            ),
            array(
                'actions' => array('addNotification'),
                'users'   => array('*'),
            ),
        );
    }

    public function index()
    {
        $this->load->model('projects/projects_model');
        $this->breadcrumb->add(lang('utility'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities');
        $month = date('m');
        $year  = date('Y');

        // Residences CR updates related code start
        $this->load->model('sites/sites_model');
        $site_id = $this->session->userdata[$this->section_name]['site_id'];
        $user_id = $this->session->userdata[$this->section_name]['user_id'];
        $site_detail = $this->sites_model->get_site_detail_custom($site_id);
        $residence_types = isset($site_detail['residence_types']) ? explode(',', $site_detail['residence_types']) : [];
        $utility_types = getUtilityConstant();
        $this->load->model('sites/site_residence_model');
        $this->site_residence_model->site_id = $site_id;
        $this->site_residence_model->user_id = $user_id;
        $this->site_residence_model->year_id  = $year;
        // Residences CR updates related code end

        if ($this->input->post()) {

            $data = $this->input->post();

            if (isset($data['month'])) {
                $month = $this->input->post('month');
            }

            if (isset($data['year'])) {
                $year = $this->input->post('year');
            }

            $this->site_residence_model->year_id  = $year;
            
            if ($data['submit'] == '1') {
                // Save form here
                $postdata                               = $this->input->post();
                $postdata['user_id']                    = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                $postdata['site_id']                    = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
                $postdata['files'] = $_FILES;
                
                $this->utilities_model->utilities_month = $month;
                $this->utilities_model->utilities_year  = $year;

                $postdata = $this->site_residence_model->residenceBlockLogic($postdata, $utility_types, $site_detail, $this->site_residence_model, $residence_types);

                // Residences CR updates related code end
                $this->utilities_model->saveElectricityTariff($postdata);
                $this->utilities_model->saveUtility($postdata);
                $this->theme->set_message(lang('utility-save-success'), 'success');

                //delete notification
                $fieldNamesArray = array();
                $this->fieldsArray = getNotificationStaticList($postdata['site_id']);
                foreach ($this->fieldsArray as $key => $value) {
                    array_push($fieldNamesArray, $key);
                }

                $electricity_tariff = $this->utilities_model->getNotificationElectricityTariff();
                if (!empty($electricity_tariff) or ($electricity_tariff[0]['kwh_sum'] > 0)) {
                    $data = array('site_id' => $postdata['site_id'],
                        'field_name'            => 'electricity_tariff',
                        'month'                 => $month,
                        'year'                  => $year);

                    $this->utilities_model->deleteNotification($data);
                }

                foreach ($postdata as $key => $value) {
                    if (in_array($key, $fieldNamesArray)) {
                        if ($value != '') {
                            $data = array('site_id' => $postdata['site_id'],
                                'field_name'            => $key,
                                'month'                 => $month,
                                'year'                  => $year);
                            $this->utilities_model->deleteNotification($data);
                        }
                    }
                }
            }
        }

        $this->utilities_model->utilities_month = $month;
        $this->utilities_model->utilities_year  = $year;

        $utility            = $this->utilities_model->getUtility();
        $electricity_tariff = $this->utilities_model->getElectricityTariff();

        $path = site_url() . "/assets/uploads/site_".$this->utilities_model->site_id."/utility_invoices/";
        $utility['electricity_invoice_scan']       = ($utility['electricity_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['electricity_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; // $path.$utility['electricity_invoice_scan']
        $utility['fuel_oil_invoice_scan']          = ($utility['fuel_oil_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['fuel_oil_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $utility['lpg_invoice_scan']               = ($utility['lpg_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['lpg_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $utility['natural_gas_invoice_scan']       = ($utility['natural_gas_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['natural_gas_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $utility['district_heating_invoice_scan']  = ($utility['district_heating_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['district_heating_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $utility['district_cooling_invoice_scan']  = ($utility['district_cooling_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['district_cooling_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $utility['water_invoice_scan']             = ($utility['water_invoice_scan'] != '') ? $this->multipleInvoicePath($path, $utility['water_invoice_scan']) : site_url() . "/assets/uploads/no-image-available.jpg"; 
        
        // Residences CR updates related code start
        $residence_data = [];
        if(isset($residence_types) && !empty($residence_types)) {
            foreach ($utility_types as $key => $value) {
                $this->site_residence_model->utility_type  = $key;
                $site_residence_result = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
                $site_residence_result = $site_residence_result[0]['s'];
                if (isset($site_residence_result) && !empty($site_residence_result)) {
                    $residence_data[$key] = $site_residence_result;
                } 
            }
        }
        // Residences CR updates related code end

        $data['id']              = $utility['id'];
        $data['utility']         = $utility;
        $data['tariffs']         = $electricity_tariff;
        $data['utilities_month'] = $month;
        $data['utilities_year']  = $year;
        $data['role_id']         = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
        // Residences CR updates related code start
        $data['residence_types'] = $residence_types;
        $data['residence_data'] = $residence_data;
        // Residences CR updates related code end
        $data['site_detail'] = $site_detail;
        $this->breadcrumb->add(lang('utilities'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities');
        $this->theme->set('page_title', lang('utility'));
        $this->theme->view($data);
    }

    public function daily()
    {
        if (!UTILITIES_DAILY_MENU) {
            redirect("/utilities");
        }
        $this->load->model('projects/projects_model');
        $this->breadcrumb->add(lang('utility'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities/daily');

        $month = date('m');
        $year  = date('Y');
        $date  = date('d');
        if ($this->input->post()) {
            $data = $this->input->post();

            if (isset($data['month'])) {
                $month = $this->input->post('month');
            }

            if (isset($data['year'])) {
                $year = $this->input->post('year');
            }

            if (isset($data['date'])) {
                $date = $this->input->post('date');
            }

            if (isset($data['get_date']) && $data['get_date'] == 1) {
                $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                echo $month . "###" . $year . "###" . $number;
                exit;
            }

            if ($data['submit'] == '1') {
                $post_date_id = $this->input->post('date');
                if (!empty($post_date_id)) {
                    // Save form here
                    $postdata            = $this->input->post();
                    $postdata['user_id'] = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                    $postdata['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;

                    $this->utilities_model->utilities_date  = $date;
                    $this->utilities_model->utilities_month = $month;
                    $this->utilities_model->utilities_year  = $year;
                    $this->utilities_model->saveUtilityDaily($postdata);
                    $this->theme->set_message(lang('utility-save-success'), 'success');
                }
            }
        }

        $this->utilities_model->utilities_month = $month;
        $this->utilities_model->utilities_year  = $year;
        $this->utilities_model->utilities_date  = $date;
        $electricity_tariff = 0;  

        $utility                 = $this->utilities_model->getUtilityDaily();
        $data['date_id']         = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $data['id']              = $utility['id'];
        $data['utility']         = $utility;
        $data['tariffs']         = $electricity_tariff;
        $data['utilities_month'] = $month;
        $data['utilities_year']  = $year;
        $data['utilities_date']  = $date;
        $data['role_id']         = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
        //$data['projects'] = $this->projects_model->get_projects_helper();

        $this->load->model('sites/sites_model');
        $site_id             = $this->session->userdata[$this->section_name]['site_id'];
        $data['site_detail'] = $this->sites_model->get_site_detail_custom($site_id);

        $this->breadcrumb->add(lang('utilities'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities');
        $this->theme->set('page_title', lang('utility'));
        $this->theme->view($data);
    }

    //notification start
    public function addNotification()
    {
        $this->load->model('sites/sites_model');
        $currentMonth = intval(date('m'));
        $currentYear  = date('Y');

        if ($currentMonth == 1) {
            $currentYear  = $currentYear - 1;
            $currentMonth = 10;
        } else if($currentMonth == 2) {
            $currentYear  = $currentYear - 1;
            $currentMonth = 11;
        } else if($currentMonth == 3) {
            $currentYear  = $currentYear - 1;
            $currentMonth = 12;
        } else {
            $currentMonth = $currentMonth - 3;
            if($currentMonth < 1) {
                $currentMonth = intval(date('m')) - 1;
            }
        }

        //get all sites
        $sites                                  = $this->sites_model->get_sites();
        $this->utilities_model->utilities_month = $currentMonth;
        $this->utilities_model->utilities_year  = $currentYear;


        if ($sites) {
            for ($i = 0; $i < count($sites); $i++) {
                $utilityData = [];
                $this->utilities_model->site_id  = $sites[$i]['id'];
                // Clear all notification for previous month and year
                $this->utilities_model->clearNotification();

                // if($sites[$i]['id'] == 116) {
                $fieldNamesArray = array(); 
                //array of default field names and labels
                $this->fieldsArray = getNotificationStaticList($sites[$i]['id']);
                foreach ($this->fieldsArray as $key => $value) {
                    array_push($fieldNamesArray, $key);
                }
                $this->utilities_model->site_id = $sites[$i]['id'];

                // Get sites config
                $utility_notification_config_result = $this->utilities_model->getNotificationSiteConfig();

                $utility_notification_config_array = array();
                if (!empty($utility_notification_config_result)) {
                    foreach ($utility_notification_config_result as $key => $value) {
                        $utility_notification_config_array[] = $value['notification_title'];
                    }
                }

                $electricity_tariff = $this->utilities_model->getNotificationElectricityTariff();
                if (in_array('electricity_tariff', $utility_notification_config_array)) {
                    if (empty($electricity_tariff) or ($electricity_tariff[0]['kwh_sum'] <= 0)) {
                        $electricityData = array(
                            'site_id'     => $sites[$i]['id'],
                            'field_name'  => 'electricity_tariff',
                            'field_label' => 'Electricity purchase',
                            'month'       => $currentMonth,
                            'year'        => $currentYear,
                        );

                        $this->utilities_model->addNotification($electricityData);
                    }
                }

                $utility = $this->utilities_model->getNotificationUtility();
                if (!empty($utility)) {
                    foreach ($utility as $key => $value) {
                        if (in_array($key, $utility_notification_config_array)) {
                            $value1 = (int) $value;
                            if (empty($value1) or ($value1 <= 0) && !empty($this->fieldsArray[$key])) {
                                $utilityData = array(
                                    'site_id'     => $sites[$i]['id'],
                                    'field_name'  => $key,
                                    'field_label' => $this->fieldsArray[$key],
                                    'month'       => $currentMonth,
                                    'year'        => $currentYear,
                                );
                                $this->utilities_model->addNotification($utilityData);
                            }
                        }
                    }
                } else {
                    foreach ($this->fieldsArray as $key => $value) {
                        if (in_array($key, $utility_notification_config_array)) {
                            $value1 = (int) $value;
                            if(empty($value1) or ($value1 <= 0) && !empty($this->fieldsArray[$key])) {
                                $utilityData = array(
                                    'site_id'     => $sites[$i]['id'],
                                    'field_name'  => $key,
                                    'field_label' => $this->fieldsArray[$key],
                                    'month'       => $currentMonth,
                                    'year'        => $currentYear,
                                );

                                $this->utilities_model->addNotification($utilityData);
                            }
                        }
                    }
                }
            // }
            }
        }

        exit;
    }

    public function quarterly()
    {
        if (!UTILITIES_DAILY_MENU) {
            redirect("/utilities");
        }

        $this->load->model('projects/projects_model');
        $this->breadcrumb->add(lang('utility'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities/quarterly');

        $month = date('m');
        $year  = date('Y');

        if( ($month == '01') || ($month == '02') || ($month == '03'))
        {
            $quarter = 'Q1';
        }
        elseif( ($month == '04') || ($month == '05') || ($month == '06'))
        {
            $quarter = 'Q2';
        }
        elseif( ($month == '07') || ($month == '08') || ($month == '09'))
        {
            $quarter = 'Q3';
        }
        elseif( ($month == '10') || ($month == '11') || ($month == '12'))
        {
            $quarter = 'Q4';
        }

        if ($this->input->post()) {
           
            $data = $this->input->post();

            if (isset($data['quarter'])) {
                $quarter = $this->input->post('quarter');
                $this->utilities_model->utilities_quarter_quarter = $quarter;
            }

            if (isset($data['year'])) {
                $year = $this->input->post('year');                
                $this->utilities_model->utilities_quarter_year  = $year;     
            }

            if($data['ajaxpost'] == '1')
            {
                $this->utilities_model->utilities_quarter_quarter = $quarter;
                $this->utilities_model->utilities_quarter_year    = $year;

                $csr_data   = $this->utilities_model->getCSRwithActions();
                $hr_data    = $this->utilities_model->get_hr();
                $waste_data = $this->utilities_model->get_waste();
                $bio_data   = $this->utilities_model->get_biodiversity_with_images();

                $path = site_url() . "/assets/uploads/site_".$this->utilities_model->site_id."/waste_invoices/";
                
                $waste_data['pete_waste_invoice_scan']  = ($waste_data['pete_waste_invoice_scan'] != '') ? $path.$waste_data['pete_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['pete_recycled_invoice_scan']  = ($waste_data['pete_recycled_invoice_scan'] != '') ? $path.$waste_data['pete_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['hdpe_waste_invoice_scan']  = ($waste_data['hdpe_waste_invoice_scan'] != '') ? $path.$waste_data['hdpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['hdpe_recycled_invoice_scan']  = ($waste_data['hdpe_recycled_invoice_scan'] != '') ? $path.$waste_data['hdpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['pvc_waste_invoice_scan']  = ($waste_data['pvc_waste_invoice_scan'] != '') ? $path.$waste_data['pvc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['pvc_recycled_invoice_scan']  = ($waste_data['pvc_recycled_invoice_scan'] != '') ? $path.$waste_data['pvc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['ldpe_waste_invoice_scan']  = ($waste_data['ldpe_waste_invoice_scan'] != '') ? $path.$waste_data['ldpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['ldpe_recycled_invoice_scan']  = ($waste_data['ldpe_recycled_invoice_scan'] != '') ? $path.$waste_data['ldpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['pp_waste_invoice_scan']  = ($waste_data['pp_waste_invoice_scan'] != '') ? $path.$waste_data['pp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['pp_recycled_invoice_scan']  = ($waste_data['pp_recycled_invoice_scan'] != '') ? $path.$waste_data['pp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['ps_waste_invoice_scan']  = ($waste_data['ps_waste_invoice_scan'] != '') ? $path.$waste_data['ps_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['ps_recycled_invoice_scan']  = ($waste_data['ps_recycled_invoice_scan'] != '') ? $path.$waste_data['ps_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['op_waste_invoice_scan']  = ($waste_data['op_waste_invoice_scan'] != '') ? $path.$waste_data['op_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['op_recycled_invoice_scan']  = ($waste_data['op_recycled_invoice_scan'] != '') ? $path.$waste_data['op_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['fw_waste_invoice_scan']  = ($waste_data['fw_waste_invoice_scan'] != '') ? $path.$waste_data['fw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['fw_recycled_invoice_scan']  = ($waste_data['fw_recycled_invoice_scan'] != '') ? $path.$waste_data['fw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['glass_waste_invoice_scan']  = ($waste_data['glass_waste_invoice_scan'] != '') ? $path.$waste_data['glass_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['glass_recycled_invoice_scan']  = ($waste_data['glass_recycled_invoice_scan'] != '') ? $path.$waste_data['glass_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['wh_waste_invoice_scan']  = ($waste_data['wh_waste_invoice_scan'] != '') ? $path.$waste_data['wh_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['wh_recycled_invoice_scan']  = ($waste_data['wh_recycled_invoice_scan'] != '') ? $path.$waste_data['wh_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['wg_waste_invoice_scan']  = ($waste_data['wg_waste_invoice_scan'] != '') ? $path.$waste_data['wg_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['wg_recycled_invoice_scan']  = ($waste_data['wg_recycled_invoice_scan'] != '') ? $path.$waste_data['wg_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['wuko_waste_invoice_scan']  = ($waste_data['wuko_waste_invoice_scan'] != '') ? $path.$waste_data['wuko_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['wuko_recycled_invoice_scan']  = ($waste_data['wuko_recycled_invoice_scan'] != '') ? $path.$waste_data['wuko_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['wp_waste_invoice_scan']  = ($waste_data['wp_waste_invoice_scan'] != '') ? $path.$waste_data['wp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['wp_recycled_invoice_scan']  = ($waste_data['wp_recycled_invoice_scan'] != '') ? $path.$waste_data['wp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['wc_waste_invoice_scan']  = ($waste_data['wc_waste_invoice_scan'] != '') ? $path.$waste_data['wc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['wc_recycled_invoice_scan']  = ($waste_data['wc_recycled_invoice_scan'] != '') ? $path.$waste_data['wc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                $waste_data['gw_waste_invoice_scan']  = ($waste_data['gw_waste_invoice_scan'] != '') ? $path.$waste_data['gw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";
                $waste_data['gw_recycled_invoice_scan']  = ($waste_data['gw_recycled_invoice_scan'] != '') ? $path.$waste_data['gw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg";

                // $data['id']              = $csr_data['id'];
                $data['csr_data']        = $csr_data;
                $data['hr_data']         = $hr_data;
                $data['waste_data']      = $waste_data;
               
                $data['utilities_quarter_quarter']  = $quarter;
                $data['utilities_quarter_year']     = $year;

                $data['role_id']         = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
               
                $this->load->model('sites/sites_model');
                $site_id             = $this->session->userdata[$this->section_name]['site_id'];
                $data['site_detail'] = $this->sites_model->get_site_detail_custom($site_id);

                $i = 0;
                $n = 0;
                $ajaxdata ="<ul  style='padding: 10px;' id='main_ul'>";

                if(empty($csr_data))
                { 
                    $ajaxdata .="<li class='ngo_block_li ngo_first_li' name='ngo_block[".$i."]'>
                                        </li>";
                }

                foreach ($csr_data as $csr_key => $csr_detail) 
                {
                    $csr_detail_id = $csr_detail['id'];
                    $csr_detail_ngo_name =$csr_detail['ngo_name'];

                    $ajaxdata .= " 
                    <li class='ngo_block_li ngo_first_li' name=ngo_block[".$i."]' style='border: 2px solid #dbdbdb !important; padding: 10px;'>
                        <ul class='form-outer-block'>
                            <li>
                                <div class='row'>
                                    <div class='form-col-1' style='float: right;margin-right: 30px;'><button type='button' class='btn btn-default close_ngo' data-id='".$csr_detail_id."' data-j ='".$i."'  >Delete</button> </div>
                                </div>
                            </li>
                            <li>
                                <label class='main-label col-sm-3'>Name of Affiliate NGO </label> 
                                <div class='row'>
                                    <div class='form-col-6'>
                                        <input type='text' id='ngo_name' name='ngo_name[".$i."]' class='input-control '  value='".$csr_detail_ngo_name."' required >
                                        <input type='hidden' id='ngo_id' name='ngo_id[".$i."]' class='input-control '  value='".$csr_detail_id."'> 
                                    </div>
                                </div>
                                <div style='clear: both !important;'></div>
                            </li>

                            <li>
                            <label class='main-label col-sm-1'> Action </label>";
                            
                            $actions = $csr_detail['actions'];
                            $n = 0;
                            if (!empty($actions)) {
                                
                                foreach ($actions as $key => $action) {     
                            
                                    $action_id = $action['id'];
                                    
                                    $ajaxdata .= "<div class='action_row add-row' style='border: 1px solid #ccc; padding: 10px; '>
                                        <div class='row add-row'>
                                            <div class='form col-sm-2 form-col-add'>Description</div>
                                            <div class='form col-sm-6 form-col-add'>
                                                <input name='action_text[".$i."][]' type='text' class='input-control action-text-addition '  value='".$action['text']."'>
                                            </div>
                                            <div class='form col-sm-1 form-col-add'>SDG</div>
                                            <div class='form col-sm-2 form-col-add'>
                                                <input name='action_sdg[".$i."][]' type='text' class='input-control  '  value='".$action['sdg']."'>
                                            </div>  
                                        </div>  

                                        <div class='row add-row'>
                                            <div class='form col-sm-1 form-col-add'>Photos</div>   
                                        </div>
                                        <div class='row add-row'>               
                                            <ul class='thumbnails'> ";

                                            $site_id = $action['site_id'];
                                            $media_files = $action['media'];
                                            if (!empty($media_files)) {
                                                foreach ($media_files as $key1 => $media) {

                                                    $action_id = $media['action_id'];
                                                    $image     = $media['image'];

                                                    $path = site_url() . '/assets/uploads/site_'.$site_id.'/actions/'.$action_id.'/'.$image;
                                                    $k    = strrpos($image, '.');
                                                    if (!$k) {  $ext = ''; }

                                                    $l   = strlen($image) - $k;
                                                    $ext = substr($image, $k + 1, $l);

                                                    if(($ext == 'png') || ($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'gif'))
                                                    {    
                                                      $ajaxdata .= "<div class='form-col-1 form-col-add ' style='width: 100px; height: 100px; border: 2px solid #dbdbdb !important;margin: 10px;'>

                                                            <a class='close delete_media' href='#' data-id='".$media['id']."'>×</a>
                                                            <img src='".$path."' style='width: 80px; height: auto;' >
                                                        </div>";
                                               
                                                    }
                                                }
                                            } $ajaxdata .="
                                            </ul>               
                                        </div>  
                                        <div class='row add-row'>
                                            <div class='form-col-1 form-col-add'>Videos</div>   
                                        </div>
                                        <div class='row add-row'>               
                                            <ul class='thumbnails'>";
                                            
                                            $site_id = $action['site_id'];
                                            $media_files = $action['media'];
                                            if (!empty($media_files)) {
                                                foreach ($media_files as $key1 => $media) {

                                                    $action_id = $media['action_id'];
                                                    $image     = $media['image'];

                                                    $path = site_url() . '/assets/uploads/site_'.$site_id.'/actions/'.$action_id.'/'.$image;
                                                    $k    = strrpos($image, '.');
                                                    if (!$k) {  $ext = ''; }

                                                    $l   = strlen($image) - $k;
                                                    $ext = substr($image, $k + 1, $l);

                                                    if(($ext == 'mp3') || ($ext == 'mp4') || ($ext == 'wma'))
                                                    {  
                                                        $ajaxdata .= "<div class='form-col-6 form-col-add ' style=' border: 2px solid #dbdbdb !important;margin: 10px;'>

                                                             <a target='_blank' href='".$path."' >Click here to play video 
                                                                </a>
                                                            <a href='#' class='close delete_media' data-id='".$media['id']."'>× </a>
                                                        </div>";
                                                
                                                    }
                                                }
                                            } 
                                            $ajaxdata .= "</ul>
                                       
                                        </div>

                                        <div class='row add-row'>

                                            <div class='form col-md-10 form-col-add'>
                                                <input name='action_media[".$i."][".$n."][]' type='file' class='custom-file-upload action-addition ' value='".$action['photo']."' multiple>

                                                <input name='action_id[".$i."][]' value='".$action['id']."' type='hidden' />
                                            </div>
                                            ";
                                            if ($key == 0) 
                                            { 
                                                $ajaxdata .= "<div class='form-col-1'>
                                                    <button class='btn-control addition-plus' data-id='$i' data-cnt='".$n."' type='button' ><img src='".base_url()."themes/default/images/plus-icon.png' alt='Plus'></button>
                                                </div>";
                                             
                                            } else { 
                                                $ajaxdata .= " <div class='form-col-1'>
                                                    <button type='button' class='btn-control substract_minus ' data-id='".$action_id."'><img alt='Minus' src='".base_url()."themes/default/images/minus-icon.png'></button>
                                                </div>";
                                            } 

                                        $ajaxdata .= "</div>
                                    </div>";
                                    
                                    $n++;
                                }
                            } 
                            else 
                            {                                
                                $ajaxdata .= "
                                <div class='action_row add-row' style='border: 1px solid #ccc; padding: 10px; '>
                                    <div class='row add-row'>
                                        <div class='form col-sm-2 form-col-add'>Description</div>
                                        <div class='form col-sm-6 form-col-add'>
                                            <input name='action_text[".$i."][]' type='text' class='input-control action-text-addition '>
                                        </div>
                                        <div class='form col-sm-1 form-col-add'>SDG</div>
                                        <div class='form col-sm-2 form-col-add'>
                                            <input name='action_sdg[".$i."][]' type='text' class='input-control  '>
                                        </div>
                                    </div>

                                    <div class='row add-row'>
                                        <div class='form-col-2 form-col-add'>Photos</div>
                                        <div class='form col-md-10 form-col-add'>
                                            <input name='action_media[".$i."][0][]' type='file' class='custom-file-upload  action-addition ' value='".$action['photo']."' multiple>
                                        </div>
                                        <div class='form-col-2 form-col-add'>
                                            <button class='btn-control addition-plus' data-id='".$i."'  type='button' data-cnt='".$n."' ><img src='".base_url()."themes/default/images/plus-icon.png' alt='Plus'></button>
                                        </div>
                                    </div>    
                                </div>";
                            } 

                            $ajaxdata .= "
                            <input type='hidden' name='action_count' class='action_count' value='".$n."'>
                        </li>
                        </ul>
                        <input type='hidden' name='number_sequence' class='number_sequence' value='".$i."'>

                    </li>  ";

                    $i++;
                }

                $ajaxdata .= "
                    <input type='hidden' name='ajax_count_opendiv' id='ajax_count_opendiv' value='".$i."'>
                    <input type='hidden' id='quarter' name='quarter' value='".$quarter."' />
                    <input type='hidden' id='year' name='year' value='".$year."' />
                </ul>";

                $biodiversity_data = '';

                if(!empty($bio_data))
                { 
                    $b = 0; 
                    $bn = 0;
                    $id = '';
                    $b = count($bio_data);
                    foreach ($bio_data as $bio_key => $csr_bio) 
                    {
                        $bio_id = $csr_bio['id']; 

                        $biodiversity_data .= "<div class='biodiversity_div add-row' id='biodiversity_div' style='border: 1px solid #ccc; padding: 10px;'>
                            
                            <div class='row'> 
                                <div class='form-col-1 form-col-add'>Measure </div>
                                <div class='form col-sm-4 form-col-add'><input type='text' class='input-control' name='measure[".$bn."]' value='".$csr_bio['measure']."' ></div>
                                <label class='main-label rightLabel col-sm-1'>Partners</label>
                                <div class='form col-sm-4 form-col-add'>
                                  <input  type='text' class='input-control ' name='partner[".$bn."]' value='".$csr_bio['partner']."' > 
                                </div>
                            </div>";

                        $biodiversity_data .= "<div class='row add-row'>
                                    <div class='form col-sm-1 form-col-add'>Photos</div>   
                                </div>
                            <div class='row add-row'>";

                            $site_id = $csr_bio['site_id'];
                            $media_files = $csr_bio['media'];
                            if (!empty($media_files)) {
                                foreach ($media_files as $key1 => $media) {

                                    $bio_id = $media['biodiversity_id'];
                                    $image  = $media['image'];

                                    $path = site_url() . '/assets/uploads/site_'.$site_id.'/biodiversity/'.$bio_id.'/'.$image;
                                    $k    = strrpos($image, '.');
                                    if (!$k) {  $ext = ''; }

                                    $l   = strlen($image) - $k;
                                    $ext = substr($image, $k + 1, $l);

                                    if(($ext == 'png') || ($ext == 'jpg') || ($ext == 'jpeg') || ($ext == 'gif'))
                                    {    
                                        $biodiversity_data .= "<div class='form-col-1 form-col-add ' style='width: 100px; height: 100px; border: 2px solid #dbdbdb !important;margin: 10px;'>

                                                <a class='close delete_media_bio' href='javascript:void(0)' data-id='".$media['id']."'>×</a>
                                                <img src='".$path."' style='width: 80px; height: auto;' >
                                            </div>";
                                    }
                                }
                            }
                        $biodiversity_data .= "</div>";

                        $biodiversity_data .= "<div class='row add-row'>
                                    <div class='form col-sm-1 form-col-add'>Videos</div>   
                                </div>
                            <div class='row add-row'>";
                                $site_id = $csr_bio['site_id'];
                                $media_files = $csr_bio['media'];
                                if (!empty($media_files)) {
                                    foreach ($media_files as $key1 => $media) {

                                        $bio_id = $media['biodiversity_id'];
                                        $image  = $media['image'];

                                        $path = site_url() . '/assets/uploads/site_'.$site_id.'/biodiversity/'.$bio_id.'/'.$image;
                                        $k    = strrpos($image, '.');
                                        if (!$k) {  $ext = ''; }

                                        $l   = strlen($image) - $k;
                                        $ext = substr($image, $k + 1, $l);

                                        if(($ext == 'mp3') || ($ext == 'mp4') || ($ext == 'wma'))
                                        {    
                                            $biodiversity_data .= "<div class='form-col-6 form-col-add ' style=' border: 2px solid #dbdbdb !important;margin: 10px;'>
                                                     <a target='_blank' href='".$path."' >Click here to play video </a>
                                                    <a href='javascript:void(0)' class='close delete_media' data-id='".$media['id']."'>× </a>
                                                </div>";
                                        }
                                    }
                                }
                        $biodiversity_data .= "</div>";
                    
                        $biodiversity_data .= "
                                    <div class='row '>
                                        <div class='form col-md-10 form-col-add'>
                                            <input type='file' class='custom-file-upload file_upload bio_media'  name='bio_media[".$bn."][]'' multiple>
                                            <input name='bio_id[".$bn."]' type='hidden' class='bio_id' value='".$csr_bio['id']."' >
                                        </div> 
                                       ";

                        if($bn == 0)
                        {
                            // $btn = base_url()."themes/default/images/plus-icon.png";
                            $biodiversity_data .= "<div class='form-col-1'>
                                                    <button type='button' class='btn-control add-biodiversity' data-id=".$b." ><img src='".base_url()."themes/default/images/plus-icon.png' alt='Plus'></button>
                                                </div>
                                                <div class='form-col-1'>
                                                    <button type='button' class='btn-control substract_minus_biodiversity top_right_delete_button' data-id='".$csr_bio['id']."'><img alt='Minus' src='".base_url()."themes/default/images/minus-icon.png'></button>
                                                </div>";
                        } 
                        else 
                        { 
                            // $btn = base_url()."themes/default/images/minus-icon.png";
                            $biodiversity_data .= "
                                        <div class='form-col-1'>
                                            <button type='button' class='btn-control substract_minus_biodiversity top_right_delete_button' data-id='".$csr_bio['id']."'><img alt='Minus' src='".base_url()."themes/default/images/minus-icon.png'></button>
                                        </div>
                                        <div class='form-col-1' style='display:none;'>
                                                <button type='button' class='btn-control add-biodiversity' data-id=".$b." ><img src='".base_url()."themes/default/images/plus-icon.png' alt='Plus'></button>
                                        </div>";
                        } 

                        $biodiversity_data .= "</div>";
                        $biodiversity_data .= "</div>";

                        $bn++;
                    }
                }
                else
                {
                    $b = 0; 
                    $bn = 0;
                    $id = '';

                    $biodiversity_data .= "<div class='biodiversity_div add-row' id='biodiversity_div' style='border: 1px solid #ccc; padding: 10px;'>

                        <div class='row '>
                            <div class='form-col-1 form-col-add'>Measure </div>
                            <div class='form col-sm-4 form-col-add'><input type='text' class='input-control ' name='measure[".$bn."]'></div>
                            <label class='main-label rightLabel col-sm-1'>Partners</label>
                            <div class='form col-sm-4 form-col-add'>
                              <input  type='text' class='input-control ' name='partner[".$bn."]'> 
                            </div>
                        </div>
                        <div class='row '>
                            <div class='form-col-2 form-col-add'>Photos / Videos</div>
                            <div class='form col-md-10 form-col-add'><input type='file' class='custom-file-upload file_upload bio_media'  name='bio_media[".$bn."][]' multiple>
                            <input name='bio_id[".$bn."]'  type='hidden' class='bio_id'/>
                            </div> 
                            <div class='form-col-1'>
                                <button type='button' class='btn-control add-biodiversity' data-id='<?php echo $b; ?>' ><img src='".base_url()."themes/default/images/plus-icon.png' alt='Plus'></button>
                            </div>
                        </div>
                    </div>";
                }

                // echo  $ajaxdata;
                $tabs_data = array('social' => $ajaxdata, 'hr' => $hr_data, 'waste' => $waste_data, 'biodiversity' => $biodiversity_data);

                echo json_encode($tabs_data);
                die;
            }
                

            if ($data['submit'] == '1') {
              
                // Save form here
                $postdata            = $this->input->post();
                
                $postdata['user_id'] = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                $postdata['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
                $postdata['files'] = $_FILES;

                $this->utilities_model->utilities_quarter_quarter = $quarter;
                $this->utilities_model->utilities_quarter_year  = $year;               
                $msg = $this->utilities_model->save_ngo($postdata);
                $this->theme->set_message($msg, 'success');
                redirect("/utilities/quarterly");
            }

            // to save HR data 
            if ($data['submit'] == '2') {
              
                // Save form here
                $postdata = $this->input->post();
                
                $postdata['user_id'] = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                $postdata['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
                
                $this->utilities_model->utilities_quarter_quarter = $quarter;
                $this->utilities_model->utilities_quarter_year  = $year;               
                $msg = $this->utilities_model->save_hr($postdata);
                $this->theme->set_message('Data Saved Successfully', 'success');
                redirect("/utilities/quarterly#energy-tabs2");
            }

            // to save HR data 
            if ($data['submit'] == '3') {
              
                // Save form here
                $postdata = $this->input->post();
                $postdata['files'] = $_FILES;
                
                $postdata['user_id'] = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                $postdata['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
                
                $this->utilities_model->utilities_quarter_quarter = $quarter;
                $this->utilities_model->utilities_quarter_year  = $year;               
                $msg = $this->utilities_model->save_waste($postdata);
                $this->theme->set_message('Data Saved Successfully', 'success');
                redirect("/utilities/quarterly#energy-tabs3");
            }

            // to save biodiversity data 
            if ($data['submit'] == '4') {
              
                // Save form here
                $postdata = $this->input->post();
                $postdata['files'] = $_FILES;

                $postdata['user_id'] = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
                $postdata['site_id'] = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
                
                $this->utilities_model->utilities_quarter_quarter = $quarter;
                $this->utilities_model->utilities_quarter_year  = $year;               
                $msg = $this->utilities_model->save_biodiversity($postdata);
                $this->theme->set_message($msg, 'success');
                redirect("/utilities/quarterly#energy-tabs4");
            }
        }
 
        $this->utilities_model->utilities_quarter_quarter = $quarter;
        $this->utilities_model->utilities_quarter_year    = $year;

        $csr_data           = $this->utilities_model->getCSRwithActions(); 
        $data['id']         = $csr_data['id'];
        $data['csr_data']   = $csr_data;
        $csr_hr_data        = $this->utilities_model->get_hr();
        $csr_waste_data     = $this->utilities_model->get_waste();
        $data['csr_biodiversity'] = $this->utilities_model->get_biodiversity_with_images(); 

        $csr_hr = $csr_hr_data;
        $csr_waste = $csr_waste_data;

        // For CSR HR tab data 
        $csr_hr['hr_no_of_hrs']  = isset($csr_hr_data['hr_no_of_hrs']) ? $csr_hr_data['hr_no_of_hrs'] : '';
        $csr_hr['hr_no_of_employees']                  = isset($csr_hr_data['hr_no_of_employees']) ? $csr_hr_data['hr_no_of_employees'] : '';
        $csr_hr['nd_no_of_incidents_of_discrimination']    = isset($csr_hr_data['nd_no_of_incidents_of_discrimination']) ? $csr_hr_data['nd_no_of_incidents_of_discrimination'] : '';
        $csr_hr['nd_incident_reviewed_by_org']    = isset($csr_hr_data['nd_incident_reviewed_by_org']) ? $csr_hr_data['nd_incident_reviewed_by_org'] : '';
        $csr_hr['nd_remediation_plans_implemented']    = isset($csr_hr_data['nd_remediation_plans_implemented']) ? $csr_hr_data['nd_remediation_plans_implemented'] : '';
        $csr_hr['lpd_hires_age_under_thirty']    = isset($csr_hr_data['lpd_hires_age_under_thirty']) ? $csr_hr_data['lpd_hires_age_under_thirty'] : '';
        $csr_hr['lpd_hires_age_between_thirty_to_fifty']    = isset($csr_hr_data['lpd_hires_age_between_thirty_to_fifty']) ? $csr_hr_data['lpd_hires_age_between_thirty_to_fifty'] : '';
        $csr_hr['lpd_hires_age_more_than_fifty']    = isset($csr_hr_data['lpd_hires_age_more_than_fifty']) ? $csr_hr_data['lpd_hires_age_more_than_fifty'] : '';
        $csr_hr['lpd_hires_gender_male']    = isset($csr_hr_data['lpd_hires_gender_male']) ? $csr_hr_data['lpd_hires_gender_male'] : '';
        $csr_hr['lpd_hires_gender_female']    = isset($csr_hr_data['lpd_hires_gender_female']) ? $csr_hr_data['lpd_hires_gender_female'] : '';
        $csr_hr['lpd_turnover_age_under_thirty']    = isset($csr_hr_data['lpd_turnover_age_under_thirty']) ? $csr_hr_data['lpd_turnover_age_under_thirty'] : '';
        $csr_hr['lpd_turnover_age_between_thirty_to_fifty']    = isset($csr_hr_data['lpd_turnover_age_between_thirty_to_fifty']) ? $csr_hr_data['lpd_turnover_age_between_thirty_to_fifty'] : '';
        $csr_hr['lpd_turnover_age_more_than_fifty']    = isset($csr_hr_data['lpd_turnover_age_more_than_fifty']) ? $csr_hr_data['lpd_turnover_age_more_than_fifty'] : '';
        $csr_hr['lpd_turnover_gender_male']    = isset($csr_hr_data['lpd_turnover_gender_male']) ? $csr_hr_data['lpd_turnover_gender_male'] : '';
        $csr_hr['lpd_turnover_gender_female']    = isset($csr_hr_data['lpd_turnover_gender_female']) ? $csr_hr_data['lpd_turnover_gender_female'] : '';
        $csr_hr['ohs_rate_of_occupational_diseases'] = isset($csr_hr_data['ohs_rate_of_occupational_diseases']) ? $csr_hr_data['ohs_rate_of_occupational_diseases'] : '';
        $csr_hr['ohs_lost_day_rates']   = isset($csr_hr_data['ohs_lost_day_rates']) ? $csr_hr_data['ohs_lost_day_rates'] : '';
        $csr_hr['ohs_absentee_rate']    = isset($csr_hr_data['ohs_absentee_rate']) ? $csr_hr_data['ohs_absentee_rate'] : '';
        $csr_hr['ohs_gender_male']      = isset($csr_hr_data['ohs_gender_male']) ? $csr_hr_data['ohs_gender_male'] : '';
        $csr_hr['ohs_gender_female']    = isset($csr_hr_data['ohs_gender_female']) ? $csr_hr_data['ohs_gender_female'] : '';
        $csr_hr['te_gender_male']       = isset($csr_hr_data['te_gender_male']) ? $csr_hr_data['te_gender_male'] : '';
        $csr_hr['te_gender_female']     = isset($csr_hr_data['te_gender_female']) ? $csr_hr_data['te_gender_female'] : '';
        $csr_hr['te_team_member']       = isset($csr_hr_data['te_team_member']) ? $csr_hr_data['te_team_member'] : '';
        $csr_hr['te_supervisor']        = isset($csr_hr_data['te_supervisor']) ? $csr_hr_data['te_supervisor'] : '';
        $csr_hr['te_manager']           = isset($csr_hr_data['te_manager']) ? $csr_hr_data['te_manager'] : '';
        $csr_hr['te_head_of_department']    = isset($csr_hr_data['te_head_of_department']) ? $csr_hr_data['te_head_of_department'] : '';
        $csr_hr['te_assistant_head_of_department']  = isset($csr_hr_data['te_assistant_head_of_department']) ? $csr_hr_data['te_assistant_head_of_department'] : '';
        $csr_hr['te_general_manager']   = isset($csr_hr_data['te_general_manager']) ? $csr_hr_data['te_general_manager'] : '';
        $csr_hr['te_senior_manager']    = isset($csr_hr_data['te_senior_manager']) ? $csr_hr_data['te_senior_manager'] : '';
        $csr_hr['diversity_and_opportunity']    = isset($csr_hr_data['diversity_and_opportunity']) ? $csr_hr_data['diversity_and_opportunity'] : '';
        $csr_hr['ermw_gender_male']    = isset($csr_hr_data['ermw_gender_male']) ? $csr_hr_data['ermw_gender_male'] : '';
        $csr_hr['ermw_gender_female']  = isset($csr_hr_data['ermw_gender_female']) ? $csr_hr_data['ermw_gender_female'] : '';
        $csr_hr['ermw_team_member']    = isset($csr_hr_data['ermw_team_member']) ? $csr_hr_data['ermw_team_member'] : '';
        $csr_hr['ermw_supervisor']     = isset($csr_hr_data['ermw_supervisor']) ? $csr_hr_data['ermw_supervisor'] : '';
        $csr_hr['ermw_manager']    = isset($csr_hr_data['ermw_manager']) ? $csr_hr_data['ermw_manager'] : '';
        $csr_hr['ermw_head_of_department']    = isset($csr_hr_data['ermw_head_of_department']) ? $csr_hr_data['ermw_head_of_department'] : '';
        $csr_hr['ermw_assistant_head_of_department']    = isset($csr_hr_data['ermw_assistant_head_of_department']) ? $csr_hr_data['ermw_assistant_head_of_department'] : '';
        $csr_hr['ermw_senior_manager']    = isset($csr_hr_data['ermw_senior_manager']) ? $csr_hr_data['ermw_senior_manager'] : '';
        $csr_hr['ec_ratios_of_std_gender_male']    = isset($csr_hr_data['ec_ratios_of_std_gender_male']) ? $csr_hr_data['ec_ratios_of_std_gender_male'] : '';
        $csr_hr['ec_ratios_of_std_gender_female']    = isset($csr_hr_data['ec_ratios_of_std_gender_female']) ? $csr_hr_data['ec_ratios_of_std_gender_female'] : '';
        $csr_hr['ec_proportion_of_senior_management_hired']    = isset($csr_hr_data['ec_proportion_of_senior_management_hired']) ? $csr_hr_data['ec_proportion_of_senior_management_hired'] : '';
        $csr_hr['tmsr_global_index']    = isset($csr_hr_data['tmsr_global_index']) ? $csr_hr_data['tmsr_global_index'] : '';
        $csr_hr['tmsr_leadership_index']    = isset($csr_hr_data['tmsr_leadership_index']) ? $csr_hr_data['tmsr_leadership_index'] : '';
        $csr_hr['tmsr_loyalty_index']    = isset($csr_hr_data['tmsr_loyalty_index']) ? $csr_hr_data['tmsr_loyalty_index'] : '';
        $csr_hr['tmsr_other_index']    = isset($csr_hr_data['tmsr_other_index']) ? $csr_hr_data['tmsr_other_index'] : '';
        $csr_hr['talent_management']    = isset($csr_hr_data['talent_management']) ? $csr_hr_data['talent_management'] : '';
        $csr_hr['id']        = isset($csr_hr_data['id']) ? $csr_hr_data['id'] : '';
        $csr_hr['quarter']   = isset($csr_hr_data['quarter']) ? $csr_hr_data['quarter'] : '';
        $csr_hr['year']      = isset($csr_hr_data['year']) ? $csr_hr_data['year'] : '';
        $data['csr_hr']      = $csr_hr;
          
        // For CSR Waste tab data          
        $path = site_url() . "/assets/uploads/site_".$this->utilities_model->site_id."/waste_invoices/";

        $csr_waste['pete_waste_kg']  = isset($csr_waste_data['pete_waste_kg']) ? $csr_waste_data['pete_waste_kg'] : '';
        $csr_waste['pete_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['pete_cost_of_waste_removal_per_kg']) ? $csr_waste_data['pete_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['pete_waste_invoice_scan']  = ($csr_waste_data['pete_waste_invoice_scan'] != '') ? $path.$csr_waste_data['pete_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['pete_qty_recycled_kg']  = isset($csr_waste_data['pete_qty_recycled_kg']) ? $csr_waste_data['pete_qty_recycled_kg'] : '';
        $csr_waste['pete_revenue_from_recycling_per_kg']  = isset($csr_waste_data['pete_revenue_from_recycling_per_kg']) ? $csr_waste_data['pete_revenue_from_recycling_per_kg'] : '';
        $csr_waste['pete_recycled_invoice_scan']  = ($csr_waste_data['pete_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['pete_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['hdpe_waste_kg']  = isset($csr_waste_data['hdpe_waste_kg']) ? $csr_waste_data['hdpe_waste_kg'] : '';
        $csr_waste['hdpe_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['hdpe_cost_of_waste_removal_per_kg']) ? $csr_waste_data['hdpe_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['hdpe_waste_invoice_scan']  = ($csr_waste_data['hdpe_waste_invoice_scan'] != '') ? $path.$csr_waste_data['hdpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['hdpe_qty_recycled_kg']  = isset($csr_waste_data['hdpe_qty_recycled_kg']) ? $csr_waste_data['hdpe_qty_recycled_kg'] : '';
        $csr_waste['hdpe_revenue_from_recycling_per_kg']  = isset($csr_waste_data['hdpe_revenue_from_recycling_per_kg']) ? $csr_waste_data['hdpe_revenue_from_recycling_per_kg'] : '';
        $csr_waste['hdpe_recycled_invoice_scan']  = ($csr_waste_data['hdpe_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['hdpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['pvc_waste_kg']  = isset($csr_waste_data['pvc_waste_kg']) ? $csr_waste_data['pvc_waste_kg'] : '';
        $csr_waste['pvc_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['pvc_cost_of_waste_removal_per_kg']) ? $csr_waste_data['pvc_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['pvc_waste_invoice_scan']  = ($csr_waste_data['pvc_waste_invoice_scan'] != '') ? $path.$csr_waste_data['pvc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['pvc_qty_recycled_kg']  = isset($csr_waste_data['pvc_qty_recycled_kg']) ? $csr_waste_data['pvc_qty_recycled_kg'] : '';
        $csr_waste['pvc_revenue_from_recycling_per_kg']  = isset($csr_waste_data['pvc_revenue_from_recycling_per_kg']) ? $csr_waste_data['pvc_revenue_from_recycling_per_kg'] : '';
        $csr_waste['pvc_recycled_invoice_scan']  = ($csr_waste_data['pvc_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['pvc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['ldpe_waste_kg']  = isset($csr_waste_data['ldpe_waste_kg']) ? $csr_waste_data['ldpe_waste_kg'] : '';
        $csr_waste['ldpe_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['ldpe_cost_of_waste_removal_per_kg']) ? $csr_waste_data['ldpe_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['ldpe_waste_invoice_scan']  = ($csr_waste_data['ldpe_waste_invoice_scan'] != '') ? $path.$csr_waste_data['ldpe_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['ldpe_qty_recycled_kg']  = isset($csr_waste_data['ldpe_qty_recycled_kg']) ? $csr_waste_data['ldpe_qty_recycled_kg'] : '';
        $csr_waste['ldpe_revenue_from_recycling_per_kg']  = isset($csr_waste_data['ldpe_revenue_from_recycling_per_kg']) ? $csr_waste_data['ldpe_revenue_from_recycling_per_kg'] : '';
        $csr_waste['ldpe_recycled_invoice_scan']  = ($csr_waste_data['ldpe_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['ldpe_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['pp_waste_kg']  = isset($csr_waste_data['pp_waste_kg']) ? $csr_waste_data['pp_waste_kg'] : '';
        $csr_waste['pp_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['pp_cost_of_waste_removal_per_kg']) ? $csr_waste_data['pp_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['pp_waste_invoice_scan']  = ($csr_waste_data['pp_waste_invoice_scan'] != '') ? $path.$csr_waste_data['pp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['pp_qty_recycled_kg']  = isset($csr_waste_data['pp_qty_recycled_kg']) ? $csr_waste_data['pp_qty_recycled_kg'] : '';
        $csr_waste['pp_revenue_from_recycling_per_kg']  = isset($csr_waste_data['pp_revenue_from_recycling_per_kg']) ? $csr_waste_data['pp_revenue_from_recycling_per_kg'] : '';
        $csr_waste['pp_recycled_invoice_scan']  = ($csr_waste_data['pp_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['pp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['ps_waste_kg']  = isset($csr_waste_data['ps_waste_kg']) ? $csr_waste_data['ps_waste_kg'] : '';
        $csr_waste['ps_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['ps_cost_of_waste_removal_per_kg']) ? $csr_waste_data['ps_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['ps_waste_invoice_scan']  = ($csr_waste_data['ps_waste_invoice_scan'] != '') ? $path.$csr_waste_data['ps_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['ps_qty_recycled_kg']  = isset($csr_waste_data['ps_qty_recycled_kg']) ? $csr_waste_data['ps_qty_recycled_kg'] : '';
        $csr_waste['ps_revenue_from_recycling_per_kg']  = isset($csr_waste_data['ps_revenue_from_recycling_per_kg']) ? $csr_waste_data['ps_revenue_from_recycling_per_kg'] : '';
        $csr_waste['ps_recycled_invoice_scan']  = ($csr_waste_data['ps_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['ps_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['op_waste_kg']  = isset($csr_waste_data['op_waste_kg']) ? $csr_waste_data['op_waste_kg'] : '';
        $csr_waste['op_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['op_cost_of_waste_removal_per_kg']) ? $csr_waste_data['op_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['op_waste_invoice_scan']  = ($csr_waste_data['op_waste_invoice_scan'] != '') ? $path.$csr_waste_data['op_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['op_qty_recycled_kg']  = isset($csr_waste_data['op_qty_recycled_kg']) ? $csr_waste_data['op_qty_recycled_kg'] : '';
        $csr_waste['op_revenue_from_recycling_per_kg']  = isset($csr_waste_data['op_revenue_from_recycling_per_kg']) ? $csr_waste_data['op_revenue_from_recycling_per_kg'] : '';
        $csr_waste['op_recycled_invoice_scan']  = ($csr_waste_data['op_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['op_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['op_waste_kg']  = isset($csr_waste_data['op_waste_kg']) ? $csr_waste_data['op_waste_kg'] : '';
        $csr_waste['op_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['op_cost_of_waste_removal_per_kg']) ? $csr_waste_data['op_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['op_waste_invoice_scan']  = ($csr_waste_data['op_waste_invoice_scan'] != '') ? $path.$csr_waste_data['op_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['op_qty_recycled_kg']  = isset($csr_waste_data['op_qty_recycled_kg']) ? $csr_waste_data['op_qty_recycled_kg'] : '';
        $csr_waste['op_revenue_from_recycling_per_kg']  = isset($csr_waste_data['op_revenue_from_recycling_per_kg']) ? $csr_waste_data['op_revenue_from_recycling_per_kg'] : '';
        $csr_waste['op_recycled_invoice_scan']  = ($csr_waste_data['op_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['op_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['fw_waste_kg']  = isset($csr_waste_data['fw_waste_kg']) ? $csr_waste_data['fw_waste_kg'] : '';
        $csr_waste['fw_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['fw_cost_of_waste_removal_per_kg']) ? $csr_waste_data['fw_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['fw_waste_invoice_scan']  = ($csr_waste_data['fw_waste_invoice_scan'] != '') ? $path.$csr_waste_data['fw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['fw_qty_recycled_kg']  = isset($csr_waste_data['fw_qty_recycled_kg']) ? $csr_waste_data['fw_qty_recycled_kg'] : '';
        $csr_waste['fw_revenue_from_recycling_per_kg']  = isset($csr_waste_data['fw_revenue_from_recycling_per_kg']) ? $csr_waste_data['fw_revenue_from_recycling_per_kg'] : '';
        $csr_waste['fw_recycled_invoice_scan']  = ($csr_waste_data['fw_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['fw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['glass_waste_kg']  = isset($csr_waste_data['glass_waste_kg']) ? $csr_waste_data['glass_waste_kg'] : '';
        $csr_waste['glass_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['glass_cost_of_waste_removal_per_kg']) ? $csr_waste_data['glass_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['glass_waste_invoice_scan']  = ($csr_waste_data['glass_waste_invoice_scan'] != '') ? $path.$csr_waste_data['glass_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['glass_qty_recycled_kg']  = isset($csr_waste_data['glass_qty_recycled_kg']) ? $csr_waste_data['glass_qty_recycled_kg'] : '';
        $csr_waste['glass_revenue_from_recycling_per_kg']  = isset($csr_waste_data['glass_revenue_from_recycling_per_kg']) ? $csr_waste_data['glass_revenue_from_recycling_per_kg'] : '';
        $csr_waste['glass_recycled_invoice_scan']  = ($csr_waste_data['glass_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['glass_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wh_waste_kg']  = isset($csr_waste_data['wh_waste_kg']) ? $csr_waste_data['wh_waste_kg'] : '';
        $csr_waste['wh_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wh_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wh_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wh_waste_invoice_scan']  = ($csr_waste_data['wh_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wh_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wh_qty_recycled_kg']  = isset($csr_waste_data['wh_qty_recycled_kg']) ? $csr_waste_data['wh_qty_recycled_kg'] : '';
        $csr_waste['wh_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wh_revenue_from_recycling_per_kg']) ? $csr_waste_data['wh_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wh_recycled_invoice_scan']  = ($csr_waste_data['wh_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wh_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wg_waste_kg']  = isset($csr_waste_data['wg_waste_kg']) ? $csr_waste_data['wg_waste_kg'] : '';
        $csr_waste['wg_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wg_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wg_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wg_waste_invoice_scan']  = ($csr_waste_data['wg_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wg_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wg_qty_recycled_kg']  = isset($csr_waste_data['wg_qty_recycled_kg']) ? $csr_waste_data['wg_qty_recycled_kg'] : '';
        $csr_waste['wg_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wg_revenue_from_recycling_per_kg']) ? $csr_waste_data['wg_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wg_recycled_invoice_scan']  = ($csr_waste_data['wg_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wg_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wg_waste_kg']  = isset($csr_waste_data['wg_waste_kg']) ? $csr_waste_data['wg_waste_kg'] : '';
        $csr_waste['wg_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wg_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wg_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wg_waste_invoice_scan']  = ($csr_waste_data['wg_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wg_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wg_qty_recycled_kg']  = isset($csr_waste_data['wg_qty_recycled_kg']) ? $csr_waste_data['wg_qty_recycled_kg'] : '';
        $csr_waste['wg_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wg_revenue_from_recycling_per_kg']) ? $csr_waste_data['wg_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wg_recycled_invoice_scan']  = ($csr_waste_data['wg_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wg_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wuko_waste_kg']  = isset($csr_waste_data['wuko_waste_kg']) ? $csr_waste_data['wuko_waste_kg'] : '';
        $csr_waste['wuko_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wuko_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wuko_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wuko_waste_invoice_scan']  = ($csr_waste_data['wuko_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wuko_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wuko_qty_recycled_kg']  = isset($csr_waste_data['wuko_qty_recycled_kg']) ? $csr_waste_data['wuko_qty_recycled_kg'] : '';
        $csr_waste['wuko_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wuko_revenue_from_recycling_per_kg']) ? $csr_waste_data['wuko_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wuko_recycled_invoice_scan']  = ($csr_waste_data['wuko_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wuko_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wp_waste_kg']  = isset($csr_waste_data['wp_waste_kg']) ? $csr_waste_data['wp_waste_kg'] : '';
        $csr_waste['wp_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wp_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wp_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wp_waste_invoice_scan']  = ($csr_waste_data['wp_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wp_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wp_qty_recycled_kg']  = isset($csr_waste_data['wp_qty_recycled_kg']) ? $csr_waste_data['wp_qty_recycled_kg'] : '';
        $csr_waste['wp_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wp_revenue_from_recycling_per_kg']) ? $csr_waste_data['wp_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wp_recycled_invoice_scan']  = ($csr_waste_data['wp_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wp_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['wc_waste_kg']  = isset($csr_waste_data['wc_waste_kg']) ? $csr_waste_data['wc_waste_kg'] : '';
        $csr_waste['wc_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['wc_cost_of_waste_removal_per_kg']) ? $csr_waste_data['wc_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['wc_waste_invoice_scan']  = ($csr_waste_data['wc_waste_invoice_scan'] != '') ? $path.$csr_waste_data['wc_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['wc_qty_recycled_kg']  = isset($csr_waste_data['wc_qty_recycled_kg']) ? $csr_waste_data['wc_qty_recycled_kg'] : '';
        $csr_waste['wc_revenue_from_recycling_per_kg']  = isset($csr_waste_data['wc_revenue_from_recycling_per_kg']) ? $csr_waste_data['wc_revenue_from_recycling_per_kg'] : '';
        $csr_waste['wc_recycled_invoice_scan']  = ($csr_waste_data['wc_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['wc_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 

        $csr_waste['gw_waste_kg']  = isset($csr_waste_data['gw_waste_kg']) ? $csr_waste_data['gw_waste_kg'] : '';
        $csr_waste['gw_cost_of_waste_removal_per_kg']  = isset($csr_waste_data['gw_cost_of_waste_removal_per_kg']) ? $csr_waste_data['gw_cost_of_waste_removal_per_kg'] : '';
        $csr_waste['gw_waste_invoice_scan']  = ($csr_waste_data['gw_waste_invoice_scan'] != '') ? $path.$csr_waste_data['gw_waste_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        $csr_waste['gw_qty_recycled_kg']  = isset($csr_waste_data['gw_qty_recycled_kg']) ? $csr_waste_data['gw_qty_recycled_kg'] : '';
        $csr_waste['gw_revenue_from_recycling_per_kg']  = isset($csr_waste_data['gw_revenue_from_recycling_per_kg']) ? $csr_waste_data['gw_revenue_from_recycling_per_kg'] : '';
        $csr_waste['gw_recycled_invoice_scan']  = ($csr_waste_data['gw_recycled_invoice_scan'] != '') ? $path.$csr_waste_data['gw_recycled_invoice_scan'] : site_url() . "/assets/uploads/no-image-available.jpg"; 
        
        $csr_waste['id']      = isset($csr_waste_data['id']) ? $csr_waste_data['id'] : '';
        $csr_waste['quarter'] = isset($csr_waste_data['quarter']) ? $csr_waste_data['quarter'] : '';
        $csr_waste['year']    = isset($csr_waste_data['year']) ? $csr_waste_data['year'] : '';
        $data['csr_waste']    = $csr_waste;
    
        /*$data['utilities_month'] = $month;
        $data['utilities_year']  = $year;*/
        $data['utilities_quarter_quarter']  = $quarter;
        $data['utilities_quarter_year']     = $year;

        $data['utilities_date']  = $date;
        $data['role_id']         = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : 0;
       
        $this->load->model('sites/sites_model');
        $site_id             = $this->session->userdata[$this->section_name]['site_id'];
        $data['site_detail'] = $this->sites_model->get_site_detail_custom($site_id);

        $this->breadcrumb->add(lang('utilities'), base_url() . BASE_ADMIN_URL_CUSTOM . '/utilities/quarterly');
        $this->theme->set('page_title', lang('utility'));
        $this->theme->view($data);
    }

    // delete action image(single)
    public function delete_action_image()
    {
    	$id = $this->input->post('id');      
        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['image'] = $this->utilities_model->delete_image($id);           
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    //delete_biodiversity_image (single image)
    public function delete_biodiversity_image()
    {
        $id = $this->input->post('id');      
        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['image'] = $this->utilities_model->delete_biodiversity_image($id);           
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    // delete_waste_image
    public function delete_waste_image()
    {
        $id = $this->input->post('id');      
        $field = $this->input->post('field');  

        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data[$field] ='';           
            $this->db->where('id', $id);
            $this->db->update('csr_waste_data', $data);
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    // delete_utility_image
    public function delete_utility_image()
    {
        $id = $this->input->post('id');      
        $field = $this->input->post('field');  

        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data[$field] = NULL; 
            $this->db->where('id', $id);
            $this->db->update('utilities_cost', $data);
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    // delete action with media files
    public function delete_action()
    {
    	$id = $this->input->post('id');      
        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['image'] = $this->utilities_model->delete_action($id);           
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    // delete_biodiversity
    public function delete_biodiversity()
    {
        $id = $this->input->post('id');      
        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['image'] = $this->utilities_model->delete_biodiversity($id);           
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }
	
    // delete ngo with actions and media files
	public function delete_ngo()
    {
    	$id = $this->input->post('id');      
        //Type casting
        $id = intval($id);
      
        //logic
        if ($id != 0 && $id != '' && is_numeric($id))
        {
            $data['ngo'] = $this->utilities_model->delete_ngo($id);           
            $message = $this->theme->message(lang('msg_delete_success'), 'success');
        }
        else
        {
            $message = $this->theme->message(lang('invalid_id_msg'), 'error');
        }
        echo $message;
    }

    public function waste()
    {
        $this->theme->set('page_title', $this->lang->line('waste-monthly'));        
        $this->breadcrumb->add(lang('utility'), base_url() . BASE_ADMIN_URL_CUSTOM . 'utilities/waste');
        $this->breadcrumb->add($this->lang->line('waste-monthly'));
        $month = date('m');
        $year  = date('Y');
        $tabData  = getWasteTabData();
        
        $site_id = $this->session->userdata[$this->section_name]['site_id'];
        $user_id = $this->session->userdata[$this->section_name]['user_id'];
        $role_id = $this->session->userdata[$this->section_name]['role_id'];

        $this->load->model('sites/site_waste_model');
        $this->site_waste_model->site_id = $site_id;
        // $this->site_waste_model->user_id = $user_id;
        $this->site_waste_model->month_id = NULL;
        $this->site_waste_model->year_id  = NULL;
        $site_waste_display_result = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
        $site_waste_display = isset($site_waste_display_result) ? $site_waste_display_result[0]['s'] : [];

        if ($this->input->post()) {
            $postData = $this->input->post();

            if(isset($postData['MonthFormat'])) {
                $dateFormat = explode("/", $postData['MonthFormat']);
            }
            
            if (isset($dateFormat)) {
                $month = $dateFormat[0];
            }

            if (isset($dateFormat)) {
                $year = $dateFormat[1];
            }

            if($_POST['wasteFormSubmit']) {
                $this->load->model('sites/site_waste_model');
                $this->site_waste_model->site_id = $postData['site_id'];
                $this->site_waste_model->user_id = $user_id;
                $this->site_waste_model->month_id = $month;
                $this->site_waste_model->year_id  = $year;
                $this->site_waste_model->typical_destination_bottles_cans = NULL;
                $this->site_waste_model->unit_measure_dropdown_bottles_cans = NULL;
                $this->site_waste_model->source_bottles_cans = NULL;
                $this->site_waste_model->monthly_tracking_bottles_cans = NULL;
                $this->site_waste_model->unit_measure_bottles_cans = isset($site_waste_display['is_check_bottles_cans']) && isset($postData['unit_measure_bottles_cans']) ? $postData['unit_measure_bottles_cans'] : NULL;
                $this->site_waste_model->disposal_cost_bottles_cans = isset($site_waste_display['is_check_bottles_cans']) && isset($postData['disposal_cost_bottles_cans']) ? $postData['disposal_cost_bottles_cans'] : NULL;
                $this->site_waste_model->total_bottles_cans = isset($site_waste_display['is_check_bottles_cans']) && isset($postData['total_bottles_cans']) ? $postData['total_bottles_cans'] : NULL;
                $this->site_waste_model->is_check_bottles_cans = NULL;
                $this->site_waste_model->typical_destination_wastetoenergy = NULL;
                $this->site_waste_model->unit_measure_dropdown_wastetoenergy = NULL;
                $this->site_waste_model->source_wastetoenergy = NULL;
                $this->site_waste_model->monthly_tracking_wastetoenergy = NULL;
                $this->site_waste_model->unit_measure_wastetoenergy = isset($site_waste_display['is_check_wastetoenergy']) && isset($postData['unit_measure_wastetoenergy']) ? $postData['unit_measure_wastetoenergy'] : NULL;
                $this->site_waste_model->disposal_cost_wastetoenergy = isset($site_waste_display['is_check_wastetoenergy']) && isset($postData['disposal_cost_wastetoenergy']) ? $postData['disposal_cost_wastetoenergy'] : NULL;
                $this->site_waste_model->total_wastetoenergy = isset($site_waste_display['is_check_wastetoenergy']) && isset($postData['total_wastetoenergy']) ? $postData['total_wastetoenergy'] : NULL;
                $this->site_waste_model->is_check_wastetoenergy = NULL;
                $this->site_waste_model->typical_destination_cardboard = NULL;
                $this->site_waste_model->unit_measure_dropdown_cardboard = NULL;
                $this->site_waste_model->source_cardboard = NULL;
                $this->site_waste_model->monthly_tracking_cardboard = NULL;
                $this->site_waste_model->unit_measure_cardboard = isset($site_waste_display['is_check_cardboard']) && isset($postData['unit_measure_cardboard']) ? $postData['unit_measure_cardboard'] : NULL;
                $this->site_waste_model->disposal_cost_cardboard = isset($site_waste_display['is_check_cardboard']) && isset($postData['disposal_cost_cardboard']) ? $postData['disposal_cost_cardboard'] : NULL;
                $this->site_waste_model->total_cardboard = isset($site_waste_display['is_check_cardboard']) && isset($postData['total_cardboard']) ? $postData['total_cardboard'] : NULL;
                $this->site_waste_model->is_check_cardboard = NULL;
                $this->site_waste_model->typical_destination_paper = NULL;
                $this->site_waste_model->unit_measure_dropdown_paper = NULL;
                $this->site_waste_model->source_paper = NULL;
                $this->site_waste_model->monthly_tracking_paper = NULL;
                $this->site_waste_model->unit_measure_paper = isset($site_waste_display['is_check_paper']) && isset($postData['unit_measure_paper']) ? $postData['unit_measure_paper'] : NULL;
                $this->site_waste_model->disposal_cost_paper = isset($site_waste_display['is_check_paper']) && isset($postData['disposal_cost_paper']) ? $postData['disposal_cost_paper'] : NULL;
                $this->site_waste_model->total_paper = isset($site_waste_display['is_check_paper']) && isset($postData['total_paper']) ? $postData['total_paper'] : NULL;
                $this->site_waste_model->is_check_paper = NULL;
                $this->site_waste_model->typical_destination_mixed_glass = NULL;
                $this->site_waste_model->unit_measure_dropdown_mixed_glass = NULL;
                $this->site_waste_model->source_mixed_glass = NULL;
                $this->site_waste_model->monthly_tracking_mixed_glass = NULL;
                $this->site_waste_model->unit_measure_mixed_glass = isset($site_waste_display['is_check_mixed_glass']) && isset($postData['unit_measure_mixed_glass']) ? $postData['unit_measure_mixed_glass'] : NULL;
                $this->site_waste_model->disposal_cost_mixed_glass = isset($site_waste_display['is_check_mixed_glass']) && isset($postData['disposal_cost_mixed_glass']) ? $postData['disposal_cost_mixed_glass'] : NULL;
                $this->site_waste_model->total_mixed_glass = isset($site_waste_display['is_check_mixed_glass']) && isset($postData['total_mixed_glass']) ? $postData['total_mixed_glass'] : NULL;
                $this->site_waste_model->is_check_mixed_glass = NULL;
                $this->site_waste_model->typical_destination_alluminium = NULL;
                $this->site_waste_model->unit_measure_dropdown_alluminium = NULL;
                $this->site_waste_model->source_alluminium = NULL;
                $this->site_waste_model->monthly_tracking_alluminium = NULL;
                $this->site_waste_model->unit_measure_alluminium = isset($site_waste_display['is_check_alluminium']) && isset($postData['unit_measure_alluminium']) ? $postData['unit_measure_alluminium'] : NULL;
                $this->site_waste_model->disposal_cost_alluminium = isset($site_waste_display['is_check_alluminium']) && isset($postData['disposal_cost_alluminium']) ? $postData['disposal_cost_alluminium'] : NULL;
                $this->site_waste_model->total_alluminium = isset($site_waste_display['is_check_alluminium']) && isset($postData['total_alluminium']) ? $postData['total_alluminium'] : NULL;
                $this->site_waste_model->is_check_alluminium = NULL;
                $this->site_waste_model->typical_destination_pete_plastic_bottles = NULL;
                $this->site_waste_model->unit_measure_dropdown_pete_plastic_bottles = NULL;
                $this->site_waste_model->source_pete_plastic_bottles = NULL;
                $this->site_waste_model->monthly_tracking_pete_plastic_bottles = NULL;
                $this->site_waste_model->unit_measure_pete_plastic_bottles = isset($site_waste_display['is_check_pete_plastic_bottles']) && isset($postData['unit_measure_pete_plastic_bottles']) ? $postData['unit_measure_pete_plastic_bottles'] : NULL;
                $this->site_waste_model->disposal_cost_pete_plastic_bottles = isset($site_waste_display['is_check_pete_plastic_bottles']) && isset($postData['disposal_cost_pete_plastic_bottles']) ? $postData['disposal_cost_pete_plastic_bottles'] : NULL;
                $this->site_waste_model->total_pete_plastic_bottles = isset($site_waste_display['is_check_pete_plastic_bottles']) && isset($postData['total_pete_plastic_bottles']) ? $postData['total_pete_plastic_bottles'] : NULL;
                $this->site_waste_model->is_check_pete_plastic_bottles = NULL;
                $this->site_waste_model->typical_destination_hdpe = NULL;
                $this->site_waste_model->unit_measure_dropdown_hdpe = NULL;
                $this->site_waste_model->source_hdpe = NULL;
                $this->site_waste_model->monthly_tracking_hdpe = NULL;
                $this->site_waste_model->unit_measure_hdpe = isset($site_waste_display['is_check_hdpe']) && isset($postData['unit_measure_hdpe']) ? $postData['unit_measure_hdpe'] : NULL;
                $this->site_waste_model->disposal_cost_hdpe = isset($site_waste_display['is_check_hdpe']) && isset($postData['disposal_cost_hdpe']) ? $postData['disposal_cost_hdpe'] : NULL;
                $this->site_waste_model->total_hdpe = isset($site_waste_display['is_check_hdpe']) && isset($postData['total_hdpe']) ? $postData['total_hdpe'] : NULL;
                $this->site_waste_model->is_check_hdpe = NULL;
                $this->site_waste_model->typical_destination_other_plastics = NULL;
                $this->site_waste_model->unit_measure_dropdown_other_plastics = NULL;
                $this->site_waste_model->source_other_plastics = NULL;
                $this->site_waste_model->monthly_tracking_other_plastics = NULL;
                $this->site_waste_model->unit_measure_other_plastics = isset($site_waste_display['is_check_other_plastics']) && isset($postData['unit_measure_other_plastics']) ? $postData['unit_measure_other_plastics'] : NULL;
                $this->site_waste_model->disposal_cost_other_plastics = isset($site_waste_display['is_check_other_plastics']) && isset($postData['disposal_cost_other_plastics']) ? $postData['disposal_cost_other_plastics'] : NULL;
                $this->site_waste_model->total_other_plastics = isset($site_waste_display['is_check_other_plastics']) && isset($postData['total_other_plastics']) ? $postData['total_other_plastics'] : NULL;
                $this->site_waste_model->is_check_other_plastics = NULL;
                $this->site_waste_model->typical_destination_bottled_amenities = NULL;
                $this->site_waste_model->unit_measure_dropdown_bottled_amenities = NULL;
                $this->site_waste_model->source_bottled_amenities = NULL;
                $this->site_waste_model->monthly_tracking_bottled_amenities = NULL;
                $this->site_waste_model->unit_measure_bottled_amenities = isset($site_waste_display['is_check_bottled_amenities']) && isset($postData['unit_measure_bottled_amenities']) ? $postData['unit_measure_bottled_amenities'] : NULL;
                $this->site_waste_model->disposal_cost_bottled_amenities = isset($site_waste_display['is_check_bottled_amenities']) && isset($postData['disposal_cost_bottled_amenities']) ? $postData['disposal_cost_bottled_amenities'] : NULL;
                $this->site_waste_model->total_bottled_amenities = isset($site_waste_display['is_check_bottled_amenities']) && isset($postData['total_bottled_amenities']) ? $postData['total_bottled_amenities'] : NULL;
                $this->site_waste_model->is_check_bottled_amenities = NULL;
                $this->site_waste_model->typical_destination_soap_bars = NULL;
                $this->site_waste_model->unit_measure_dropdown_soap_bars = NULL;
                $this->site_waste_model->source_soap_bars = NULL;
                $this->site_waste_model->monthly_tracking_soap_bars = NULL;
                $this->site_waste_model->unit_measure_soap_bars = isset($site_waste_display['is_check_soap_bars']) && isset($postData['unit_measure_soap_bars']) ? $postData['unit_measure_soap_bars'] : NULL;
                $this->site_waste_model->disposal_cost_soap_bars = isset($site_waste_display['is_check_soap_bars']) && isset($postData['disposal_cost_soap_bars']) ? $postData['disposal_cost_soap_bars'] : NULL;
                $this->site_waste_model->total_soap_bars = isset($site_waste_display['is_check_soap_bars']) && isset($postData['total_soap_bars']) ? $postData['total_soap_bars'] : NULL;
                $this->site_waste_model->is_check_soap_bars = NULL;
                $this->site_waste_model->typical_destination_palettes_and_crates = NULL;
                $this->site_waste_model->unit_measure_dropdown_palettes_and_crates = NULL;
                $this->site_waste_model->source_palettes_and_crates = NULL;
                $this->site_waste_model->monthly_tracking_palettes_and_crates = NULL;
                $this->site_waste_model->unit_measure_palettes_and_crates = isset($site_waste_display['is_check_palettes_and_crates']) && isset($postData['unit_measure_palettes_and_crates']) ? $postData['unit_measure_palettes_and_crates'] : NULL;
                $this->site_waste_model->disposal_cost_palettes_and_crates = isset($site_waste_display['is_check_palettes_and_crates']) && isset($postData['disposal_cost_palettes_and_crates']) ? $postData['disposal_cost_palettes_and_crates'] : NULL;
                $this->site_waste_model->total_palettes_and_crates = isset($site_waste_display['is_check_palettes_and_crates']) && isset($postData['total_palettes_and_crates']) ? $postData['total_palettes_and_crates'] : NULL;
                $this->site_waste_model->is_check_palettes_and_crates = NULL;
                $this->site_waste_model->typical_destination_e_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_e_waste = NULL;
                $this->site_waste_model->source_e_waste = NULL;
                $this->site_waste_model->monthly_tracking_e_waste = NULL;
                $this->site_waste_model->unit_measure_e_waste = isset($site_waste_display['is_check_e_waste']) && isset($postData['unit_measure_e_waste']) ? $postData['unit_measure_e_waste'] : NULL;
                $this->site_waste_model->disposal_cost_e_waste = isset($site_waste_display['is_check_e_waste']) && isset($postData['disposal_cost_e_waste']) ? $postData['disposal_cost_e_waste'] : NULL;
                $this->site_waste_model->total_e_waste = isset($site_waste_display['is_check_e_waste']) && isset($postData['total_e_waste']) ? $postData['total_e_waste'] : NULL;
                $this->site_waste_model->is_check_e_waste = NULL;
                $this->site_waste_model->typical_destination_durable_goods = NULL;
                $this->site_waste_model->unit_measure_dropdown_durable_goods = NULL;
                $this->site_waste_model->source_durable_goods = NULL;
                $this->site_waste_model->monthly_tracking_durable_goods = NULL;
                $this->site_waste_model->unit_measure_durable_goods = isset($site_waste_display['is_check_durable_goods']) && isset($postData['unit_measure_durable_goods']) ? $postData['unit_measure_durable_goods'] : NULL;
                $this->site_waste_model->disposal_cost_durable_goods = isset($site_waste_display['is_check_durable_goods']) && isset($postData['disposal_cost_durable_goods']) ? $postData['disposal_cost_durable_goods'] : NULL;
                $this->site_waste_model->total_durable_goods = isset($site_waste_display['is_check_durable_goods']) && isset($postData['total_durable_goods']) ? $postData['total_durable_goods'] : NULL;
                $this->site_waste_model->is_check_durable_goods = NULL;
                $this->site_waste_model->typical_destination_solid_food_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_solid_food_waste = NULL;
                $this->site_waste_model->source_solid_food_waste = NULL;
                $this->site_waste_model->monthly_tracking_solid_food_waste = NULL;
                $this->site_waste_model->unit_measure_solid_food_waste = isset($site_waste_display['is_check_solid_food_waste']) && isset($postData['unit_measure_solid_food_waste']) ? $postData['unit_measure_solid_food_waste'] : NULL;
                $this->site_waste_model->disposal_cost_solid_food_waste = isset($site_waste_display['is_check_solid_food_waste']) && isset($postData['disposal_cost_solid_food_waste']) ? $postData['disposal_cost_solid_food_waste'] : NULL;
                $this->site_waste_model->total_solid_food_waste = isset($site_waste_display['is_check_solid_food_waste']) && isset($postData['total_solid_food_waste']) ? $postData['total_solid_food_waste'] : NULL;
                $this->site_waste_model->is_check_solid_food_waste = NULL;
                $this->site_waste_model->typical_destination_leftover_food = NULL;
                $this->site_waste_model->unit_measure_dropdown_leftover_food = NULL;
                $this->site_waste_model->source_leftover_food = NULL;
                $this->site_waste_model->monthly_tracking_leftover_food = NULL;
                $this->site_waste_model->unit_measure_leftover_food = isset($site_waste_display['is_check_leftover_food']) && isset($postData['unit_measure_leftover_food']) ? $postData['unit_measure_leftover_food'] : NULL;
                $this->site_waste_model->disposal_cost_leftover_food = isset($site_waste_display['is_check_leftover_food']) && isset($postData['disposal_cost_leftover_food']) ? $postData['disposal_cost_leftover_food'] : NULL;
                $this->site_waste_model->total_leftover_food = isset($site_waste_display['is_check_leftover_food']) && isset($postData['total_leftover_food']) ? $postData['total_leftover_food'] : NULL;
                $this->site_waste_model->is_check_leftover_food = NULL;
                $this->site_waste_model->typical_destination_inedible_parts = NULL;
                $this->site_waste_model->unit_measure_dropdown_inedible_parts = NULL;
                $this->site_waste_model->source_inedible_parts = NULL;
                $this->site_waste_model->monthly_tracking_inedible_parts = NULL;
                $this->site_waste_model->unit_measure_inedible_parts = isset($site_waste_display['is_check_inedible_parts']) && isset($postData['unit_measure_inedible_parts']) ? $postData['unit_measure_inedible_parts'] : NULL;
                $this->site_waste_model->disposal_cost_inedible_parts = isset($site_waste_display['is_check_inedible_parts']) && isset($postData['disposal_cost_inedible_parts']) ? $postData['disposal_cost_inedible_parts'] : NULL;
                $this->site_waste_model->total_inedible_parts = isset($site_waste_display['is_check_inedible_parts']) && isset($postData['total_inedible_parts']) ? $postData['total_inedible_parts'] : NULL;
                $this->site_waste_model->is_check_inedible_parts = NULL;
                $this->site_waste_model->typical_destination_liquid_food_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_liquid_food_waste = NULL;
                $this->site_waste_model->source_liquid_food_waste = NULL;
                $this->site_waste_model->monthly_tracking_liquid_food_waste = NULL;
                $this->site_waste_model->unit_measure_liquid_food_waste = isset($site_waste_display['is_check_liquid_food_waste']) && isset($postData['unit_measure_liquid_food_waste']) ? $postData['unit_measure_liquid_food_waste'] : NULL;
                $this->site_waste_model->disposal_cost_liquid_food_waste = isset($site_waste_display['is_check_liquid_food_waste']) && isset($postData['disposal_cost_liquid_food_waste']) ? $postData['disposal_cost_liquid_food_waste'] : NULL;
                $this->site_waste_model->total_liquid_food_waste = isset($site_waste_display['is_check_liquid_food_waste']) && isset($postData['total_liquid_food_waste']) ? $postData['total_liquid_food_waste'] : NULL;
                $this->site_waste_model->is_check_liquid_food_waste = NULL;
                $this->site_waste_model->typical_destination_kitchen_grease = NULL;
                $this->site_waste_model->unit_measure_dropdown_kitchen_grease = NULL;
                $this->site_waste_model->source_kitchen_grease = NULL;
                $this->site_waste_model->monthly_tracking_kitchen_grease = NULL;
                $this->site_waste_model->unit_measure_kitchen_grease = isset($site_waste_display['is_check_kitchen_grease']) && isset($postData['unit_measure_kitchen_grease']) ? $postData['unit_measure_kitchen_grease'] : NULL;
                $this->site_waste_model->disposal_cost_kitchen_grease = isset($site_waste_display['is_check_kitchen_grease']) && isset($postData['disposal_cost_kitchen_grease']) ? $postData['disposal_cost_kitchen_grease'] : NULL;
                $this->site_waste_model->total_kitchen_grease = isset($site_waste_display['is_check_kitchen_grease']) && isset($postData['total_kitchen_grease']) ? $postData['total_kitchen_grease'] : NULL;
                $this->site_waste_model->is_check_kitchen_grease = NULL;
                $this->site_waste_model->typical_destination_liquid_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_liquid_hazardous_waste = NULL;
                $this->site_waste_model->source_liquid_hazardous_waste = NULL;
                $this->site_waste_model->monthly_tracking_liquid_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_liquid_hazardous_waste = isset($site_waste_display['is_check_liquid_hazardous_waste']) && isset($postData['unit_measure_liquid_hazardous_waste']) ? $postData['unit_measure_liquid_hazardous_waste'] : NULL;
                $this->site_waste_model->disposal_cost_liquid_hazardous_waste = isset($site_waste_display['is_check_liquid_hazardous_waste']) && isset($postData['disposal_cost_liquid_hazardous_waste']) ? $postData['disposal_cost_liquid_hazardous_waste'] : NULL;
                $this->site_waste_model->total_liquid_hazardous_waste = isset($site_waste_display['is_check_liquid_hazardous_waste']) && isset($postData['total_liquid_hazardous_waste']) ? $postData['total_liquid_hazardous_waste'] : NULL;
                $this->site_waste_model->is_check_liquid_hazardous_waste = NULL;
                $this->site_waste_model->typical_destination_other_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_other_hazardous_waste = NULL;
                $this->site_waste_model->source_other_hazardous_waste = NULL;
                $this->site_waste_model->monthly_tracking_other_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_other_hazardous_waste = isset($site_waste_display['is_check_other_hazardous_waste']) && isset($postData['unit_measure_other_hazardous_waste']) ? $postData['unit_measure_other_hazardous_waste'] : NULL;
                $this->site_waste_model->disposal_cost_other_hazardous_waste = isset($site_waste_display['is_check_other_hazardous_waste']) && isset($postData['disposal_cost_other_hazardous_waste']) ? $postData['disposal_cost_other_hazardous_waste'] : NULL;
                $this->site_waste_model->total_other_hazardous_waste = isset($site_waste_display['is_check_other_hazardous_waste']) && isset($postData['total_other_hazardous_waste']) ? $postData['total_other_hazardous_waste'] : NULL;
                $this->site_waste_model->is_check_other_hazardous_waste = NULL;
                $this->site_waste_model->typical_destination_batteries = NULL;
                $this->site_waste_model->unit_measure_dropdown_batteries = NULL;
                $this->site_waste_model->source_batteries = NULL;
                $this->site_waste_model->monthly_tracking_batteries = NULL;
                $this->site_waste_model->unit_measure_batteries = isset($site_waste_display['is_check_batteries']) && isset($postData['unit_measure_batteries']) ? $postData['unit_measure_batteries'] : NULL;
                $this->site_waste_model->disposal_cost_batteries = isset($site_waste_display['is_check_batteries']) && isset($postData['disposal_cost_batteries']) ? $postData['disposal_cost_batteries'] : NULL;
                $this->site_waste_model->total_batteries = isset($site_waste_display['is_check_batteries']) && isset($postData['total_batteries']) ? $postData['total_batteries'] : NULL;
                $this->site_waste_model->is_check_batteries = NULL;
                $this->site_waste_model->typical_destination_light_bulbs = NULL;
                $this->site_waste_model->unit_measure_dropdown_light_bulbs = NULL;
                $this->site_waste_model->source_light_bulbs = NULL;
                $this->site_waste_model->monthly_tracking_light_bulbs = NULL;
                $this->site_waste_model->unit_measure_light_bulbs = isset($site_waste_display['is_check_light_bulbs']) && isset($postData['unit_measure_light_bulbs']) ? $postData['unit_measure_light_bulbs'] : NULL;
                $this->site_waste_model->disposal_cost_light_bulbs = isset($site_waste_display['is_check_light_bulbs']) && isset($postData['disposal_cost_light_bulbs']) ? $postData['disposal_cost_light_bulbs'] : NULL;
                $this->site_waste_model->total_light_bulbs = isset($site_waste_display['is_check_light_bulbs']) && isset($postData['total_light_bulbs']) ? $postData['total_light_bulbs'] : NULL;
                $this->site_waste_model->is_check_light_bulbs = NULL;
                $this->site_waste_model->typical_destination_light_fixtures = NULL;
                $this->site_waste_model->unit_measure_dropdown_light_fixtures = NULL;
                $this->site_waste_model->source_light_fixtures = NULL;
                $this->site_waste_model->monthly_tracking_light_fixtures = NULL;
                $this->site_waste_model->unit_measure_light_fixtures = isset($site_waste_display['is_check_light_fixtures']) && isset($postData['unit_measure_light_fixtures']) ? $postData['unit_measure_light_fixtures'] : NULL;
                $this->site_waste_model->disposal_cost_light_fixtures = isset($site_waste_display['is_check_light_fixtures']) && isset($postData['disposal_cost_light_fixtures']) ? $postData['disposal_cost_light_fixtures'] : NULL;
                $this->site_waste_model->total_light_fixtures = isset($site_waste_display['is_check_light_fixtures']) && isset($postData['total_light_fixtures']) ? $postData['total_light_fixtures'] : NULL;
                $this->site_waste_model->is_check_light_fixtures = NULL;
                $this->site_waste_model->typical_destination_textiles = NULL;
                $this->site_waste_model->unit_measure_dropdown_textiles = NULL;
                $this->site_waste_model->source_textiles = NULL;
                $this->site_waste_model->monthly_tracking_textiles = NULL;
                $this->site_waste_model->unit_measure_textiles = isset($site_waste_display['is_check_textiles']) && isset($postData['unit_measure_textiles']) ? $postData['unit_measure_textiles'] : NULL;
                $this->site_waste_model->disposal_cost_textiles = isset($site_waste_display['is_check_textiles']) && isset($postData['disposal_cost_textiles']) ? $postData['disposal_cost_textiles'] : NULL;
                $this->site_waste_model->total_textiles = isset($site_waste_display['is_check_textiles']) && isset($postData['total_textiles']) ? $postData['total_textiles'] : NULL;
                $this->site_waste_model->is_check_textiles = NULL;
                $this->site_waste_model->typical_destination_wood = NULL;
                $this->site_waste_model->unit_measure_dropdown_wood = NULL;
                $this->site_waste_model->source_wood = NULL;
                $this->site_waste_model->monthly_tracking_wood = NULL;
                $this->site_waste_model->unit_measure_wood = isset($site_waste_display['is_check_wood']) && isset($postData['unit_measure_wood']) ? $postData['unit_measure_wood'] : NULL;
                $this->site_waste_model->disposal_cost_wood = isset($site_waste_display['is_check_wood']) && isset($postData['disposal_cost_wood']) ? $postData['disposal_cost_wood'] : NULL;
                $this->site_waste_model->total_wood = isset($site_waste_display['is_check_wood']) && isset($postData['total_wood']) ? $postData['total_wood'] : NULL;
                $this->site_waste_model->is_check_wood = NULL;
                $this->site_waste_model->typical_destination_building_constructions = NULL;
                $this->site_waste_model->unit_measure_dropdown_building_constructions = NULL;
                $this->site_waste_model->source_building_constructions = NULL;
                $this->site_waste_model->monthly_tracking_building_constructions = NULL;
                $this->site_waste_model->unit_measure_building_constructions = isset($site_waste_display['is_check_building_constructions']) && isset($postData['unit_measure_building_constructions']) ? $postData['unit_measure_building_constructions'] : NULL;
                $this->site_waste_model->disposal_cost_building_constructions = isset($site_waste_display['is_check_building_constructions']) && isset($postData['disposal_cost_building_constructions']) ? $postData['disposal_cost_building_constructions'] : NULL;
                $this->site_waste_model->total_building_constructions = isset($site_waste_display['is_check_building_constructions']) && isset($postData['total_building_constructions']) ? $postData['total_building_constructions'] : NULL;
                $this->site_waste_model->is_check_building_constructions = NULL;
                $this->site_waste_model->typical_destination_other = NULL;
                $this->site_waste_model->unit_measure_dropdown_other = NULL;
                $this->site_waste_model->source_other = NULL;
                $this->site_waste_model->monthly_tracking_other = NULL;
                $this->site_waste_model->unit_measure_other = isset($site_waste_display['is_check_other']) && isset($postData['unit_measure_other']) ? $postData['unit_measure_other'] : NULL;
                $this->site_waste_model->disposal_cost_other = isset($site_waste_display['is_check_other']) && isset($postData['disposal_cost_other']) ? $postData['disposal_cost_other'] : NULL;
                $this->site_waste_model->total_other = isset($site_waste_display['is_check_other']) && isset($postData['total_other']) ? $postData['total_other'] : NULL;
                $this->site_waste_model->is_check_other = NULL;
                $this->site_waste_model->typical_destination_recycling = NULL;
                $this->site_waste_model->unit_measure_dropdown_recycling = NULL;
                $this->site_waste_model->source_recycling = NULL;
                $this->site_waste_model->monthly_tracking_recycling = NULL;
                $this->site_waste_model->unit_measure_recycling = isset($site_waste_display['is_check_recycling']) && isset($postData['unit_measure_recycling']) ? $postData['unit_measure_recycling'] : NULL;
                $this->site_waste_model->disposal_cost_recycling = isset($site_waste_display['is_check_recycling']) && isset($postData['disposal_cost_recycling']) ? $postData['disposal_cost_recycling'] : NULL;
                $this->site_waste_model->total_recycling = isset($site_waste_display['is_check_recycling']) && isset($postData['total_recycling']) ? $postData['total_recycling'] : NULL;
                $this->site_waste_model->is_check_recycling = NULL;
                $this->site_waste_model->typical_destination_commingled_recyclables = NULL;
                $this->site_waste_model->unit_measure_dropdown_commingled_recyclables = NULL;
                $this->site_waste_model->source_commingled_recyclables = NULL;
                $this->site_waste_model->monthly_tracking_commingled_recyclables = NULL;
                $this->site_waste_model->unit_measure_commingled_recyclables = isset($site_waste_display['is_check_commingled_recyclables']) && isset($postData['unit_measure_commingled_recyclables']) ? $postData['unit_measure_commingled_recyclables'] : NULL;
                $this->site_waste_model->disposal_cost_commingled_recyclables = isset($site_waste_display['is_check_commingled_recyclables']) && isset($postData['disposal_cost_commingled_recyclables']) ? $postData['disposal_cost_commingled_recyclables'] : NULL;
                $this->site_waste_model->total_commingled_recyclables = isset($site_waste_display['is_check_commingled_recyclables']) && isset($postData['total_commingled_recyclables']) ? $postData['total_commingled_recyclables'] : NULL;
                $this->site_waste_model->is_check_commingled_recyclables = NULL;
                $this->site_waste_model->typical_destination_paper_cardboard = NULL;
                $this->site_waste_model->unit_measure_dropdown_paper_cardboard = NULL;
                $this->site_waste_model->source_paper_cardboard = NULL;
                $this->site_waste_model->monthly_tracking_paper_cardboard = NULL;
                $this->site_waste_model->unit_measure_paper_cardboard = isset($site_waste_display['is_check_paper_cardboard']) && isset($postData['unit_measure_paper_cardboard']) ? $postData['unit_measure_paper_cardboard'] : NULL;
                $this->site_waste_model->disposal_cost_paper_cardboard = isset($site_waste_display['is_check_paper_cardboard']) && isset($postData['disposal_cost_paper_cardboard']) ? $postData['disposal_cost_paper_cardboard'] : NULL;
                $this->site_waste_model->total_paper_cardboard = isset($site_waste_display['is_check_paper_cardboard']) && isset($postData['total_paper_cardboard']) ? $postData['total_paper_cardboard'] : NULL;
                $this->site_waste_model->is_check_paper_cardboard = NULL;
                $this->site_waste_model->typical_destination_mixed_metals = NULL;
                $this->site_waste_model->unit_measure_dropdown_mixed_metals = NULL;
                $this->site_waste_model->source_mixed_metals = NULL;
                $this->site_waste_model->monthly_tracking_mixed_metals = NULL;
                $this->site_waste_model->unit_measure_mixed_metals = isset($site_waste_display['is_check_mixed_metals']) && isset($postData['unit_measure_mixed_metals']) ? $postData['unit_measure_mixed_metals'] : NULL;
                $this->site_waste_model->disposal_cost_mixed_metals = isset($site_waste_display['is_check_mixed_metals']) && isset($postData['disposal_cost_mixed_metals']) ? $postData['disposal_cost_mixed_metals'] : NULL;
                $this->site_waste_model->total_mixed_metals = isset($site_waste_display['is_check_mixed_metals']) && isset($postData['total_mixed_metals']) ? $postData['total_mixed_metals'] : NULL;
                $this->site_waste_model->is_check_mixed_metals = NULL;
                $this->site_waste_model->typical_destination_plastics = NULL;
                $this->site_waste_model->unit_measure_dropdown_plastics = NULL;
                $this->site_waste_model->source_plastics = NULL;
                $this->site_waste_model->monthly_tracking_plastics = NULL;
                $this->site_waste_model->unit_measure_plastics = isset($site_waste_display['is_check_plastics']) && isset($postData['unit_measure_plastics']) ? $postData['unit_measure_plastics'] : NULL;
                $this->site_waste_model->disposal_cost_plastics = isset($site_waste_display['is_check_plastics']) && isset($postData['disposal_cost_plastics']) ? $postData['disposal_cost_plastics'] : NULL;
                $this->site_waste_model->total_plastics = isset($site_waste_display['is_check_plastics']) && isset($postData['total_plastics']) ? $postData['total_plastics'] : NULL;
                $this->site_waste_model->is_check_plastics = NULL;
                $this->site_waste_model->typical_destination_donations = NULL;
                $this->site_waste_model->unit_measure_dropdown_donations = NULL;
                $this->site_waste_model->source_donations = NULL;
                $this->site_waste_model->monthly_tracking_donations = NULL;
                $this->site_waste_model->unit_measure_donations = isset($site_waste_display['is_check_donations']) && isset($postData['unit_measure_donations']) ? $postData['unit_measure_donations'] : NULL;
                $this->site_waste_model->disposal_cost_donations = isset($site_waste_display['is_check_donations']) && isset($postData['disposal_cost_donations']) ? $postData['disposal_cost_donations'] : NULL;
                $this->site_waste_model->total_donations = isset($site_waste_display['is_check_donations']) && isset($postData['total_donations']) ? $postData['total_donations'] : NULL;
                $this->site_waste_model->is_check_donations = NULL;
                $this->site_waste_model->typical_destination_toiletry_donations = NULL;
                $this->site_waste_model->unit_measure_dropdown_toiletry_donations = NULL;
                $this->site_waste_model->source_toiletry_donations = NULL;
                $this->site_waste_model->monthly_tracking_toiletry_donations = NULL;
                $this->site_waste_model->unit_measure_toiletry_donations = isset($site_waste_display['is_check_toiletry_donations']) && isset($postData['unit_measure_toiletry_donations']) ? $postData['unit_measure_toiletry_donations'] : NULL;
                $this->site_waste_model->disposal_cost_toiletry_donations = isset($site_waste_display['is_check_toiletry_donations']) && isset($postData['disposal_cost_toiletry_donations']) ? $postData['disposal_cost_toiletry_donations'] : NULL;
                $this->site_waste_model->total_toiletry_donations = isset($site_waste_display['is_check_toiletry_donations']) && isset($postData['total_toiletry_donations']) ? $postData['total_toiletry_donations'] : NULL;
                $this->site_waste_model->is_check_toiletry_donations = NULL;
                $this->site_waste_model->typical_destination_biodegradable = NULL;
                $this->site_waste_model->unit_measure_dropdown_biodegradable = NULL;
                $this->site_waste_model->source_biodegradable = NULL;
                $this->site_waste_model->monthly_tracking_biodegradable = NULL;
                $this->site_waste_model->unit_measure_biodegradable = isset($site_waste_display['is_check_biodegradable']) && isset($postData['unit_measure_biodegradable']) ? $postData['unit_measure_biodegradable'] : NULL;
                $this->site_waste_model->disposal_cost_biodegradable = isset($site_waste_display['is_check_biodegradable']) && isset($postData['disposal_cost_biodegradable']) ? $postData['disposal_cost_biodegradable'] : NULL;
                $this->site_waste_model->total_biodegradable = isset($site_waste_display['is_check_biodegradable']) && isset($postData['total_biodegradable']) ? $postData['total_biodegradable'] : NULL;
                $this->site_waste_model->is_check_biodegradable = NULL;
                $this->site_waste_model->typical_destination_mixed_organic = NULL;
                $this->site_waste_model->unit_measure_dropdown_mixed_organic = NULL;
                $this->site_waste_model->source_mixed_organic = NULL;
                $this->site_waste_model->monthly_tracking_mixed_organic = NULL;
                $this->site_waste_model->unit_measure_mixed_organic = isset($site_waste_display['is_check_mixed_organic']) && isset($postData['unit_measure_mixed_organic']) ? $postData['unit_measure_mixed_organic'] : NULL;
                $this->site_waste_model->disposal_cost_mixed_organic = isset($site_waste_display['is_check_mixed_organic']) && isset($postData['disposal_cost_mixed_organic']) ? $postData['disposal_cost_mixed_organic'] : NULL;
                $this->site_waste_model->total_mixed_organic = isset($site_waste_display['is_check_mixed_organic']) && isset($postData['total_mixed_organic']) ? $postData['total_mixed_organic'] : NULL;
                $this->site_waste_model->is_check_mixed_organic = NULL;
                $this->site_waste_model->typical_destination_food_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_food_waste = NULL;
                $this->site_waste_model->source_food_waste = NULL;
                $this->site_waste_model->monthly_tracking_food_waste = NULL;
                $this->site_waste_model->unit_measure_food_waste = isset($site_waste_display['is_check_food_waste']) && isset($postData['unit_measure_food_waste']) ? $postData['unit_measure_food_waste'] : NULL;
                $this->site_waste_model->disposal_cost_food_waste = isset($site_waste_display['is_check_food_waste']) && isset($postData['disposal_cost_food_waste']) ? $postData['disposal_cost_food_waste'] : NULL;
                $this->site_waste_model->total_food_waste = isset($site_waste_display['is_check_food_waste']) && isset($postData['total_food_waste']) ? $postData['total_food_waste'] : NULL;
                $this->site_waste_model->is_check_food_waste = NULL;
                $this->site_waste_model->typical_destination_landfill_other = NULL;
                $this->site_waste_model->unit_measure_dropdown_landfill_other = NULL;
                $this->site_waste_model->source_landfill_other = NULL;
                $this->site_waste_model->monthly_tracking_landfill_other = NULL;
                $this->site_waste_model->unit_measure_landfill_other = isset($site_waste_display['is_check_landfill_other']) && isset($postData['unit_measure_landfill_other']) ? $postData['unit_measure_landfill_other'] : NULL;
                $this->site_waste_model->disposal_cost_landfill_other = isset($site_waste_display['is_check_landfill_other']) && isset($postData['disposal_cost_landfill_other']) ? $postData['disposal_cost_landfill_other'] : NULL;
                $this->site_waste_model->total_landfill_other = isset($site_waste_display['is_check_landfill_other']) && isset($postData['total_landfill_other']) ? $postData['total_landfill_other'] : NULL;
                $this->site_waste_model->is_check_landfill_other = NULL;
                $this->site_waste_model->typical_destination_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_hazardous_waste = NULL;
                $this->site_waste_model->source_hazardous_waste = NULL;
                $this->site_waste_model->monthly_tracking_hazardous_waste = NULL;
                $this->site_waste_model->unit_measure_hazardous_waste = isset($site_waste_display['is_check_hazardous_waste']) && isset($postData['unit_measure_hazardous_waste']) ? $postData['unit_measure_hazardous_waste'] : NULL;
                $this->site_waste_model->disposal_cost_hazardous_waste = isset($site_waste_display['is_check_hazardous_waste']) && isset($postData['disposal_cost_hazardous_waste']) ? $postData['disposal_cost_hazardous_waste'] : NULL;
                $this->site_waste_model->total_hazardous_waste = isset($site_waste_display['is_check_hazardous_waste']) && isset($postData['total_hazardous_waste']) ? $postData['total_hazardous_waste'] : NULL;
                $this->site_waste_model->is_check_hazardous_waste = NULL;
                $this->site_waste_model->typical_destination_universal_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_universal_waste = NULL;
                $this->site_waste_model->source_universal_waste = NULL;
                $this->site_waste_model->monthly_tracking_universal_waste = NULL;
                $this->site_waste_model->unit_measure_universal_waste = isset($site_waste_display['is_check_universal_waste']) && isset($postData['unit_measure_universal_waste']) ? $postData['unit_measure_universal_waste'] : NULL;
                $this->site_waste_model->disposal_cost_universal_waste = isset($site_waste_display['is_check_universal_waste']) && isset($postData['disposal_cost_universal_waste']) ? $postData['disposal_cost_universal_waste'] : NULL;
                $this->site_waste_model->total_universal_waste = isset($site_waste_display['is_check_universal_waste']) && isset($postData['total_universal_waste']) ? $postData['total_universal_waste'] : NULL;
                $this->site_waste_model->is_check_universal_waste = NULL;
                $this->site_waste_model->typical_destination_other_materials = NULL;
                $this->site_waste_model->unit_measure_dropdown_other_materials = NULL;
                $this->site_waste_model->source_other_materials = NULL;
                $this->site_waste_model->monthly_tracking_other_materials = NULL;
                $this->site_waste_model->unit_measure_other_materials = isset($site_waste_display['is_check_other_materials']) && isset($postData['unit_measure_other_materials']) ? $postData['unit_measure_other_materials'] : NULL;
                $this->site_waste_model->disposal_cost_other_materials = isset($site_waste_display['is_check_other_materials']) && isset($postData['disposal_cost_other_materials']) ? $postData['disposal_cost_other_materials'] : NULL;
                $this->site_waste_model->total_other_materials = isset($site_waste_display['is_check_other_materials']) && isset($postData['total_other_materials']) ? $postData['total_other_materials'] : NULL;
                $this->site_waste_model->is_check_other_materials = NULL;
                $this->site_waste_model->typical_destination_hazardous_and_universal_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_hazardous_and_universal_waste = NULL;
                $this->site_waste_model->source_hazardous_and_universal_waste = NULL;
                $this->site_waste_model->monthly_tracking_hazardous_and_universal_waste = NULL;
                $this->site_waste_model->unit_measure_hazardous_and_universal_waste = isset($postData['unit_measure_hazardous_and_universal_waste']) ? $postData['unit_measure_hazardous_and_universal_waste'] : NULL;
                $this->site_waste_model->disposal_cost_hazardous_and_universal_waste = isset($postData['disposal_cost_hazardous_and_universal_waste']) ? $postData['disposal_cost_hazardous_and_universal_waste'] : NULL;
                $this->site_waste_model->total_hazardous_and_universal_waste = isset($postData['total_hazardous_and_universal_waste']) ? $postData['total_hazardous_and_universal_waste'] : NULL;
                $this->site_waste_model->is_check_hazardous_and_universal_waste = NULL;
                $this->site_waste_model->typical_destination_medical_waste = NULL;
                $this->site_waste_model->unit_measure_dropdown_medical_waste = NULL;
                $this->site_waste_model->source_medical_waste = NULL;
                $this->site_waste_model->monthly_tracking_medical_waste = NULL;
                $this->site_waste_model->unit_measure_medical_waste = isset($postData['unit_measure_medical_waste']) ? $postData['unit_measure_medical_waste'] : NULL;
                $this->site_waste_model->disposal_cost_medical_waste = isset($postData['disposal_cost_medical_waste']) ? $postData['disposal_cost_medical_waste'] : NULL;
                $this->site_waste_model->total_medical_waste = isset($postData['total_medical_waste']) ? $postData['total_medical_waste'] : NULL;
                $this->site_waste_model->is_check_medical_waste = NULL;
                $this->site_waste_model->typical_destination_tin = NULL;
                $this->site_waste_model->unit_measure_dropdown_tin = NULL;
                $this->site_waste_model->source_tin = NULL;
                $this->site_waste_model->monthly_tracking_tin = NULL;
                $this->site_waste_model->unit_measure_tin = isset($postData['unit_measure_tin']) ? $postData['unit_measure_tin'] : NULL;
                $this->site_waste_model->disposal_cost_tin = isset($postData['disposal_cost_tin']) ? $postData['disposal_cost_tin'] : NULL;
                $this->site_waste_model->total_tin = isset($postData['total_tin']) ? $postData['total_tin'] : NULL;
                $this->site_waste_model->is_check_tin = NULL;
                $this->site_waste_model->insert_site_waste();

                if (isset($site_waste) && !empty($site_waste)) {
                    $this->theme->set_message(lang('msg_update_success'), 'success');
                } else {
                    $this->theme->set_message(lang('msg_add_success'), 'success');
                }
                // redirect(BASE_ADMIN_URL_CUSTOM . 'utilities/waste');
            }

            if(isset($_FILES) && !empty($_FILES) && isset($_FILES['invoice_scan']) && isset($_FILES['invoice_scan']['size'])) {

                $this->site_waste_model->site_id = $site_id;
                $this->site_waste_model->user_id = $user_id;
                $this->site_waste_model->month_id = $month;
                $this->site_waste_model->year_id  = $year;

                $this->site_waste_model->insert_waste_invoice($_FILES);
            }
        }
        
        $this->load->model('sites/site_waste_model');
        $this->site_waste_model->month_id = $month;
        $this->site_waste_model->year_id  = $year;
        $site_waste_result = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
        $site_waste = isset($site_waste_result) ? $site_waste_result[0]['s'] : [];   
        
        $path = site_url() . "/assets/uploads/site_".$this->utilities_model->site_id."/waste_invoices/";
        $wasteInvoice = $this->site_waste_model->get_site_waste_upload_invoice();
        if(isset($wasteInvoice[0]['u'])) {
            $wasteInvoice[0]['u']['invoicePath'] = $path.$wasteInvoice[0]['u']['invoice_scan'];
        }
        $this->load->model('sites/sites_model');  
        //Variable assignments to view
        $data = array();
        $data['tab_data'] = isset($tabData) ?  $tabData : [];
        $data['site_waste'] = isset($site_waste) ?  $site_waste : [];        
        $data['site_waste_display'] = isset($site_waste_display) ?  $site_waste_display : [];  
        $data['wasteInvoice'] = isset($wasteInvoice[0]['u']) ? $wasteInvoice[0]['u'] : [];       
        $data['utilities_month'] = $month;
        $data['utilities_year']  = $year;        
        $data['site_detail'] = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);
        //Render view        
        
        $this->theme->view($data);
    }


    public function export_waste()
    {
        require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("HEP")
            ->setTitle("Waste Data")
            ->setKeywords("Waste Data");

        $style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
        $objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
        
        $site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
        $this->load->model('sites/sites_model');
        $site_detail = $this->sites_model->get_site_detail_custom($site_id);
        $this->load->model('sites/site_waste_model');
        $this->site_waste_model->site_id = $site_id;
        $wasteDataArray = $this->site_waste_model->get_site_waste_export_data();
        $this->site_waste_model->month_id = NULL;
        $this->site_waste_model->year_id  = NULL;
        $site_waste_setting = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
        $site_waste_setting = isset($site_waste_setting) ? $site_waste_setting[0]['s'] : [];
        $tabData = getWasteTabData();
        $checkWasteSettings = $wasteArray = [];
        foreach ($site_waste_setting as $key => $value) {
            if((substr( $key, 0, 9 ) == "is_check_") && $value == 1) {
                $name = substr($key, 9, strlen($key));
                $label = ucwords(str_replace('_', ' ',$name));
                $checkWasteSettings[$label] = $name;
            }
        }
        $i = 0;
        foreach ($wasteDataArray as $key => $value) {
            $wasteArray[$i]['Year'] = $value['s']['year_id'];
            $columns['Year'] = 'Year';
            $wasteArray[$i]['Month'] = $value['s']['month_id'];
            $columns['Month'] = 'Month';
            foreach ($checkWasteSettings as $label => $name) {
                $wasteArray[$i]['Total Volume/Weight '.$label] = $value['s']['unit_measure_'.$name];
                $columns['Total Volume/Weight '.$label] = 'Total Volume/Weight '.$label;
                $wasteArray[$i]['Tariff '.$label] = $value['s']['disposal_cost_'.$name];
                $columns['Tariff '.$label] = 'Tariff '.$label;
                $wasteArray[$i]['Total Cost '.$label] = $value['s']['total_'.$name];
                $columns['Total Cost '.$label] = 'Total Cost '.$label;
            }
            $i++;
        }
        $cells = array();
        $later1 = "";
        $later2 = 'A';
        $flag = 0;
        foreach ($columns as $key => $column) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
            $cells[$key] = $later1 . $later2;
            
            $objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
            if ($later2 == 'Z') {
                if ($flag == 0) {
                    $later1 = 'A';
                    $flag = 1;
                } else {
                    $later1++;
                }
                $later2 = 'A';
            } else {
                $later2++;
            }
        }
        $row = 2;
        foreach ($wasteArray as $data) {
            foreach ($data as $key => $val) {
                if (array_key_exists($key, $cells)) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
                }
            }
            $row++;
        }
        $objPHPExcel->getActiveSheet()->setAutoFilter(
            $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
        );
        ob_end_clean();
        header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Waste Data.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function multipleInvoicePath($rootPath, $filePath) {
        $Files = [];
        foreach (explode(',',$filePath) as $key => $value) {
            $Files[] = $rootPath.$value;
        }
        return implode(',',$Files);
    }
}