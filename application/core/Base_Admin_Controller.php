<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



/**

 *  Base Admin Controller

 *

 *  Base Admin controller to set settings for site's admin section.

 * 

 * @package CIDemoApplication

 *  

 * @copyright	(c) 2013, TatvaSoft

 * @author Amit Patel <amit.patel@sparsh.com>

 */

class Base_Admin_Controller extends Base_Controller {



    //Initialize

    var $data = array();



    public function __construct() {

        parent::__construct();



        global $menu_data;

        $this->section_name = 'admin';

        $this->theme->set('section_name', $this->section_name);



        $this->load->model('config_model');

        $this->load->library("my_pagination");

        $this->load->plugin('pagination');

        

        define("BASE_CURRENCY",'EURO');

        define("BASE_CURRENCY_SYMBOL",'€');



        

        $this->config_model->get_menu_array("front_menu");



        if (!isset($this->session->userdata[$this->section_name]["record_per_page"]))

            $this->session->set_custom_userdata($this->section_name, "record_per_page", RECORD_PER_PAGE);

        

        if(isset($this->session->userdata[$this->section_name]['local_currency'])){

            

            if(!empty($this->session->userdata[$this->section_name]['local_currency'])){

                define("CURRENCY",'');

                define("CURRENCY_SYMBOL",$this->session->userdata[$this->section_name]['local_currency']);



                // define("REPORT_CURRENCY",CURRENCY);

                // define("REPORT_CURRENCY_SYMBOL",$this->session->userdata[$this->section_name]['local_currency']);       

            }else{

                define("CURRENCY",BASE_CURRENCY);

                define("CURRENCY_SYMBOL",BASE_CURRENCY_SYMBOL);



                // define("REPORT_CURRENCY",BASE_CURRENCY);         

                // define("REPORT_CURRENCY_SYMBOL",BASE_CURRENCY_SYMBOL);  

            }

        }else{

            if(!isset($this->session->userdata['hep_cron_session'])){

                define("CURRENCY",BASE_CURRENCY);

                define("CURRENCY_SYMBOL",BASE_CURRENCY_SYMBOL);



                // define("REPORT_CURRENCY",BASE_CURRENCY);         

                // define("REPORT_CURRENCY_SYMBOL",BASE_CURRENCY_SYMBOL);

            }

        }

        

        define("BASE_REPORT_CURRENCY_SYMBOL",'$');    

        //language setting for Admin        

        if (!isset($this->session->userdata[$this->section_name]['site_lang_id']) || $this->session->userdata[$this->section_name]['site_lang_id'] == '') {

            $default_language = $this->languages_model->get_default_language();

            $this->site_language($default_language[0]['l']['id'], $default_language[0]['l']['language_name'], $default_language[0]['l']['language_code'], $default_language[0]['l']['direction']);

        }

        if ($this->input->post('select_language') != '') {

            $language_detail = $this->languages_model->get_languages($this->input->post('select_language'));

            $this->site_language($language_detail[0]['l']['id'], $language_detail[0]['l']['language_name'], $language_detail[0]['l']['language_code'], $language_detail[0]['l']['direction']);

        }



        if (file_exists(APPPATH . "modules/" . $this->_module . "/models/" . $this->_module . "_model" . EXT) || file_exists(APPPATH . "models/" . $this->_module . "_model" . EXT)) {

            $this->{$this->_module . '_model'}->language_id = $this->session->userdata[$this->section_name]['site_lang_id'];

        }

        $this->lang->load('application', $this->session->userdata[$this->section_name]['site_lang_name']);

        $this->lang->load($this->_module, $this->session->userdata[$this->section_name]['site_lang_name']);



        $this->set_custom_pagination_code($this->section_name);


        $siteDetail = $this->config_model->getSiteDetail($this->session->userdata[$this->section_name]['site_id']);
        $this->session->set_custom_userdata($this->section_name, "site_csr", $siteDetail[0]['csr']);
        //set default breadcrumb

        $this->breadcrumb->add(lang('home'), base_url() . 'BASE_ADMIN_URL_CUSTOM');



        if (ACTIVITY_LOG == 1) {

            $this->activity_log();

        }

    }



    /**

     * Function is_login to check user logged in to the admin site or not

     */

    protected function is_login() {

        if (isset($this->session->userdata[$this->section_name]['user_id'])) {

            return true;

        } else {

            //logout from mediawiki

            $url = base_url() . 'knowledgebase/api.php';

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('action' => 'logout',

                'format' => 'json',

                            )

                    )

            );



            // receive server response ...

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            return false;

        }

    }



    /**

     * Function to check permission of page by role

     */

    protected function check_permission() {

        // Load related modules

        $this->load->model('roles/roles_model');

        $this->load->model('permissions/permissions_model');



        if (isset($this->uri->rsegments[2]) && $this->uri->rsegments[2] == 'action') {

            $uri = get_section($this) . '.' . $this->uri->segments[1] . '.' . $this->uri->rsegments[2] . '.' . $this->uri->rsegments[3];

            //$uri = get_section($this). '.' . $this->uri->rsegments[2] . '.' . $this->uri->rsegments[3];

        } else {

            $uri = get_section($this) . '.' . $this->uri->segments[2] . '.' . $this->uri->rsegments[2];

        }





        if (isset($this->session->userdata[$this->section_name]['super_user']) && $this->session->userdata[$this->section_name]['super_user'] == '1') {

            return true;

	}
	if (isset($this->session->userdata[$this->section_name]['role_id']) && $this->session->userdata[$this->section_name]['role_id'] == '1') {            

		return true;

	}



        $result = $this->permissions_model->get_permission_id_by_name($uri);

        if (!empty($result)) {

            $role_id = (isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : '1');

            $permission_id = $result[0]['id'];

            if ($this->has_permission($role_id, $permission_id)) {

                return true;

            } else {

                return false;

            }

        } else {

            return false;

        }

    }



    /**

     * Function to has_permission to check user have permission or not

     */

    protected function has_permission($role_id = NULL, $permission_id = NULL) {

        $this->load->model('roles/roles_model');



        $permission = $this->roles_model->check_permission($role_id, $permission_id);



        if (empty($permission)) {

            return false;       //doesn't have permission

        } else {

            return true;        //has permission

        }

    }



    /**

     * Function site_language to set all language related variables in session

     */

    protected function site_language($language_id, $language_name, $language_code, $direction) {

        if (!isset($language_id) || $language_id == '') {

            show_error("No Language defind in the database");

        } else {

            $this->set_ci_session($this->section_name, 'site_lang_id', $language_id);

            $this->set_ci_session($this->section_name, 'site_lang_name', strtolower($language_name));

            $this->set_ci_session($this->section_name, 'site_lang_code', ($language_code));

            $this->set_ci_session($this->section_name, 'site_direction', ($direction));

        }

    }



}
