<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



/**

 *  Base Controller

 *

 *  base controller to set settings for whole site.

 * 

 * @package CIDemoApplication

 *  

 * @copyright	(c) 2013, TatvaSoft

 * @author Amit Patel <amit.patel@sparsh.com>

 */

class Base_Controller extends MX_Controller

{



    protected $_module;

    protected $_section_name;



    public function __construct()

    {

        parent::__construct();

        //Initialize 

        $this->theme->set_theme("default");

        $this->base_val = "Base value from base controller";

        $this->section_name = 'default';

        $this->load->library('user_agent');        

        // Load model with the [module name]_modle.php file if exist.

        $module = $this->_module = $this->router->fetch_module();

        if (file_exists(APPPATH . "modules/" . $module . "/models/" . $module . "_model" . EXT) || file_exists(APPPATH . "models/" . $module . "_model" . EXT))

        {

            $this->load->model($module . '_model', null, true);

        }

        else if (file_exists(FCPATH . "core_modules/" . $module . "/models/" . $module . "_model" . EXT))

        {

            $this->load->model($module . '_model', null, true);

        }

        

        if (file_exists(APPPATH . "modules/" . $module . "/config/config" . EXT))

        {

            $file_path = APPPATH . "modules/" . $module . "/config/config" . EXT;            

            require($file_path);

        }

        else if (file_exists(FCPATH . "core_modules/" . $module . "/config/config" . EXT))

        {

            $file_path = FCPATH . "core_modules/" . $module . "/config/config" . EXT;            

            require($file_path);

        }        

        

        //Load models

        $this->load->model('languages/languages_model');

        $this->load->model('roles/roles_model');

        $this->load->model('permissions/permissions_model');

        

        $section_name = get_section($this);

        if ($section_name == '')

        {

            $section_name = 'admin';

        }



    }



    /**

     * Function allowed_permission_list to set permission in session 

     * @param string $slug_url

     */

    protected function allowed_permission_list($role_id = 0)

    {

        //Initializing

        $i = 0;

        $user_permissions = array();

        

        if (isset($this->session->userdata[$this->section_name]['user_id']) && $this->session->userdata[$this->section_name]['user_id'])

        {

            $user_id = $this->session->userdata[$this->section_name]['user_id'];

            $user_permissions = $this->roles_model->get_user_permissions_by_id($user_id);

        }

        //echo $user_id; exit;

        if (!empty($user_permissions))

        {     

            $res = $this->permissions_model->allowed_user_permission_list($user_id);

            foreach ($res as $permission)

            {                

                $permission_array[$i] = strtolower($permission['p']['permission_label']);

                $i++;

            }            

            $this->session->set_custom_userdata($this->section_name, "permissions", $permission_array);             

            return true;

        } elseif ($role_id != 0)

        {

            $res = $this->permissions_model->allowed_permission_list($role_id);

            foreach ($res as $permission)

            {

                $permission_array[$i] = strtolower($permission['P']['permission_label']);

                $i++;

            }

            $this->session->set_custom_userdata($this->section_name, "permissions", $permission_array);

            return true;

        }

    }



    /**

     * Function activity_log to maintain activity log in database

     */

    protected function activity_log()

    {

        //Initializing

        $user_id = 0;

        $section_name = get_section($this);

        if ($section_name == '')

        {

            $section_name = 'admin';

        }

        if (isset($this->session->userdata[$section_name]['user_id']) && $this->session->userdata[$section_name]['user_id'])

        {

            $user_id = $this->session->userdata[$section_name]['user_id'];

        }

        

        //Variable assignment

        if (isset($this->session->userdata['session_id']) && $this->session->userdata['session_id']) {

            $session_id = $this->session->userdata['session_id'];

        } else {

            $session_id = '';

        }

        // $session_id = $this->session->userdata['session_id'];

        $ip_address = $this->input->ip_address();

        if ($this->input->server('QUERY_STRING'))

        {

            $url = current_url() . "?" . $this->input->server('QUERY_STRING');

        }

        else

        {



            $url = current_url();

        }

        //Pass variables to array

        $data = array(

            'user_id' => $user_id,

            'session_id' => $session_id,

            'url' => $url,

            'ip_address' => $ip_address

        );



        //Load activity_log model        

        $this->load->model('activity_log_model');

        //Calling activity_log function to insert value

        $this->activity_log_model->save_activity_log($data);

    }



    protected function set_custom_pagination_code($section_name)

    {

		

        if ($this->input->get('per_page_result'))

        {

            $this->record_per_page = intval($this->input->get('per_page_result'));

            $this->session->set_custom_userdata($this->section_name, "record_per_page", $this->record_per_page);

        }

        elseif ($this->input->post('per_page_result'))

        {

            $this->record_per_page = intval($this->input->post('per_page_result'));

            $this->session->set_custom_userdata($this->section_name, "record_per_page", $this->record_per_page);

        }

        elseif ($this->session->userdata[$section_name]['record_per_page'])

        {

            $this->record_per_page = intval($this->session->userdata[$section_name]['record_per_page']);

        }

        else

        {

            $this->record_per_page = RECORD_PER_PAGE;

        }

		

        if ($this->input->post('page_number'))

        {

            $this->page_number = $this->input->post('page_number');

        }

        else

        {

            $this->page_number = 1;

        }

        /* pagination code */

    }



    /**

     * Function set_ci_session to set sectionwise session

     */

    public function set_ci_session($section_name, $key, $value = NULL)

    {

        if (isset($value))

        {

            $session_array = array($key => $value);

            $this->session->set_custom_userdata($section_name, $session_array);

        }

    }



    public function __call($name, $arguments)

    {

        // Note: value of $name is case sensitive.

        show_error("Calling undefined method from <br />Controller: <strong>" . get_class($this) . "</strong><br />Method: <b>$name</b> <br />Arguments: " . implode(', ', $arguments) . "\n");

    }



    public function _remap($method, $params = array())

    {

        if (method_exists($this, $method))

        {

            return call_user_func_array(array($this, $method), $params);

        }



        $this->__call($method, $params);

    }



    protected function load_modules_model($model)

    {

        if ($model != "")

        {

            $module_path = "";

            $module = explode('_', $model);

            $module = $module[0];

            foreach (Modules::$locations as $location => $offset)

            {

                /* only add a module path if it exists */

                if (is_dir($module_path = $location . $module . '/models/'))

                {

                    break;

                }

            }



            Modules::load_file($model, $module_path);

            return true;

        }

        return false;

    }



    /**

     * Function accessControl to check the page access

     */

    protected function access_control($accessRules)

    {

        

        $allowAccess = false;

        $haspermission = "-1";

        foreach ($accessRules as $key => $rulesArray)

        {

            $method_name = get_method($this);

            

            $section_name = get_section($this);

            

            $controller_name = get_controller($this);



            $section_name_session = $this->section_name;

            if ($section_name_session == '')

            {

                $section_name_session = 'admin';

            }

            if (is_array($rulesArray['actions']) && in_array(trim($method_name), $rulesArray['actions']))

            {

                if ($rulesArray['users'][0] == '*')

                {

                    $allowAccess = true;

                }

                elseif ($rulesArray['users'][0] == '@')

                {

                    if (isset($this->session->userdata[$section_name_session]['user_id']))

                    {   

                        //return true;

                        if ($this->check_permission_uri())

                        {

                            $allowAccess = true;

                            return true;

                        }

                        else

                        {

                            $haspermission = 0;

                        }

                    }

                }

            }

        }

       

        if ($allowAccess == false)

        {   

            if ($this->input->is_ajax_request())

            {                   

                if ($haspermission == '0')

                {                    

                    echo show_permission_error(lang('permission-error'),200, lang('permission-error-msg'));      

                    exit;

                }

                else

                {

                    echo show_permission_error(lang('permission-error'),200, lang('permission-error-login-msg'));      

                    exit;

                }

            }

            else

            {

                // Set messages

                if ($haspermission == '0')

                {

                    $this->theme->set_message(lang('permission-error-msg'), 'error');                

                }

                else

                {

                    $this->theme->set_message(lang('permission-error-login-msg'), 'error');                

                }

                // Set routing if permission fails

                if ($section_name == '' && $haspermission == "-1")

                {

                    redirect($this->router->routes['default_controller']);

                }

                elseif ($section_name == '' && $haspermission == "0")

                {

                    redirect("/");

                }

                elseif ($section_name != '' && $haspermission == "0")

                {

                    //redirect("/" . BASE_ADMIN_URL_CUSTOM."/users");

                    redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');

                }

                else

                {

                    //redirect("/" . $section_name . "/users/login");

                    redirect("/" . BASE_ADMIN_URL_CUSTOM . "/users/login/?back_url=".  urlencode(current_url()));

                }

            }



            // Set routing if permission fails

            if ($section_name == '' && $haspermission == "-1")

            {

                redirect($this->router->routes['default_controller']);

            }

            elseif ($section_name == '' && $haspermission == "0")

            {

                redirect("/");

            }

            elseif ($section_name != '' && $haspermission == "0")

            {

                //redirect("/" . $this->section_name."/users/index");

                //redirect("/" . BASE_ADMIN_URL_CUSTOM."/users/index");                

                redirect(BASE_ADMIN_URL_CUSTOM . 'dashboard');

            }

            else

            {

                //echo current_url();exit;

                redirect("/" .$this->section_name . "/users/login/?back_url=".  urlencode(current_url()));

            }            



        }

        return $allowAccess;

    }



    /**

     * Function check_permission_uri to check is user has permission to access the method or not

     */

    private function check_permission_uri()

    {        

        $section_name = get_section($this);

        

        if ($section_name == '')

        {

            $section_name = 'admin';

        }

         

        $controller_name = get_controller($this);

        $method_name = get_method($this);

        $module_name = get_module($this);

        $uri = $section_name . '.' . $module_name . '.' . $method_name;

        $front_uri = 'front.' . $module_name . '.' . $method_name;



        $permission_array = $this->session->userdata[$this->section_name]['permissions'];

        if (isset($this->session->userdata[$this->section_name]['super_user']) && $this->session->userdata[$this->section_name]['super_user'] == '1') {            

            return true;

        }
        if (isset($this->session->userdata[$this->section_name]['role_id']) && $this->session->userdata[$this->section_name]['role_id'] == '1') {            

            return true;

        }

        

        if (is_array($permission_array)) {            

            if (count(preg_grep("/$uri/", $permission_array)) > 0 && get_parent_class($this) == "Base_Admin_Controller") {

                return true;

            } else if($controller_name == "forum" && get_parent_class($this) == "Base_Front_Controller" && count(preg_grep("/$front_uri/", $permission_array)) > 0) {

                return true;

            } else {

                return false;

            }

        } else {

            return false;

        }

    }



}
