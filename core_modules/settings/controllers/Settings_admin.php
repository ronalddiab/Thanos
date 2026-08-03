<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Setting Admin Controller
 *
 *  General setting for an application
 * 
 * @package CIDemoApplication
 *  
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Settings_admin extends Base_Admin_Controller {

    /**
     * 	Create an instance
     */
    function __construct() {
        parent::__construct();

        //Logic
        $this->access_control($this->access_rules());

        //view variable assignment        
        $this->breadcrumb->add('Settings', base_url() . 'settings');
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('index', 'action', 'add', 'edit', 'delete', 'save', 'view_data'),
                'users' => array('@'),
            )
        );
    }

    /**
     * index action to listout general settings
     */
    function index() {

        //variable assignment
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->settings_model->record_per_page = $this->record_per_page;
        $this->settings_model->offset = $offset;

        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['search_term'])) {
                $this->settings_model->search_term = $data['search_term'];
            }
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->settings_model->sort_by = $data['sort_by'];
                $this->settings_model->sort_order = $data['sort_order'];
            }
        }

        // load data for url listing 
        $settings = $this->settings_model->get_settings();
        $this->settings_model->_record_count = true;
        $total_records = $this->settings_model->get_settings();
        // Pass data to view file     
        $data = array(
            'settings' => $settings,
            'page_number' => $this->page_number,
            'total_records' => $total_records,
            'search_term' => $this->settings_model->search_term,
            'sort_by' => $this->settings_model->sort_by,
            'sort_order' => $this->settings_model->sort_order
        );

        //Create page-title
        $this->theme->set('page_title', lang('setting'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * Function action is used for fetching add or edit view as for given parameters
     * @param type $action default = 'add'
     * @param type $id     default = '0'
     */
    function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting / Exception
            $id = intval($id);
            $action = strip_tags($action);
            custom_filter_input('integer', $id);

            //initialization variables
            $setting_title = "";
            $setting_label = "";
            $setting_value = "";
            $comment = "";

            switch ($action) {
                case 'add': break;
                case 'edit':
                    //Check whether record exist or not?
                    if (is_numeric($id) && $id > 0) {
                        $selsettings = $this->settings_model->get_setting_by_id($id);

                        if (!empty($selsettings)) {
                            //retrive row one
                            $selsettings = $selsettings[0];

                            //take alias from an array
                            $alias = end(array_keys($selsettings));

                            $setting_title = $selsettings[$alias]['setting_title'];
                            $setting_label = $selsettings[$alias]['setting_label'];
                            $setting_value = $selsettings[$alias]['setting_value'];
                            $comment = $selsettings[$alias]['comment'];
                        } else {
                            //redirect user to list page
                            redirect(BASE_ADMIN_URL_CUSTOM . 'settings/index/');
                        }
                    }
                    break;
                default :
                    $this->theme->set_message('This action is not allowed', 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'settings');
                    break;
            }

            // Pass data to view file     
            $data = array(
                'id' => $id,
                'setting_title' => $setting_title,
                'setting_label' => $setting_label,
                'setting_value' => $setting_value,
                'comment' => $comment
            );

            //create breadcrumbs
            if ($action == 'add') {
                $this->theme->set('page_title', lang('settings-add'));
                $this->breadcrumb->add(lang('settings-add'));
            } else {
                $this->theme->set('page_title', lang('settings-edit'));
                $this->breadcrumb->add(lang('settings-edit'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'users');
            exit;
        }
    }

    function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');
        $data = $this->input->post();
        $id = isset($data['id']) ? intval($data['id']) : 0;

        //variable assignment
        $setting_title = isset($data['setting_title']) ? trim(strip_tags($data['setting_title'])) : '';
        $setting_label = isset($data['setting_label']) ? trim(strip_tags($data['setting_label'])) : '';
        $setting_value = isset($data['setting_value']) ? trim(strip_tags($data['setting_value'])) : '';
        $comment = isset($data['comment']) ? trim(strip_tags($data['comment'])) : '';

        if ($this->input->post()) {
            //set server side logic
            $this->form_validation->set_rules('setting_title', 'Setting Title', 'trim|required|is_unique[settings.setting_title.id.' . $id . ']|xss-clean');
            $this->form_validation->set_rules('setting_label', 'Setting Label', 'trim|required|is_unique[settings.setting_label.id.' . $id . ']|xss-clean');
            $this->form_validation->set_rules('setting_value', 'Setting Value', 'trim|required|xss-clean');
            $this->form_validation->set_rules('comment', 'Setting Value', 'trim|xss-clean');

            if ($this->form_validation->run()) {
                //logic
                $dataVal = array(
                    'id' => $id,
                    'setting_title' => $setting_title,
                    'setting_label' => $setting_label,
                    'setting_value' => $setting_value,
                    'comment' => $comment
                );


                //generate settings dynamically
                if ($this->generate_settings() == true) {
                    $this->settings_model->save_settings($dataVal);
                    if ($id == 0) {
                        $this->theme->set_message(lang('setting-message-add'), 'success');
                    } else {
                        $this->theme->set_message(lang('setting-message-update'), 'success');
                    }

                    //redirect user to listing page
                    redirect(BASE_ADMIN_URL_CUSTOM . 'settings');
                } else {
                    if ($id == 0) {
                        $this->theme->set_message(lang('setting-message-add1') . ' File: application/config/custom_config.php', 'error');
                    } else {
                        $this->theme->set_message(lang('setting-message-update1') . ' File: application/config/custom_config.php', 'error');
                    }
                }
            }
        }
        //set default set value on save action                
        //variable assigment to view
        if ($id == 0 || $id == '') {
            $this->theme->set('page_title', lang('settings-add'));
            $this->breadcrumb->add(lang('settings-add'));
        } else {
            $this->theme->set('page_title', lang('settings-edit'));
            $this->breadcrumb->add(lang('settings-add'));
        }

        //pass variable
        $data = array(
            'id' => $id,
            'setting_title' => $setting_title,
            'setting_label' => $setting_label,
            'setting_value' => $setting_value,
            'comment' => $comment
        );

        //render view if any error
        $this->theme->view($data, 'admin_add');
    }

    function delete() {
        if ($this->input->post()) {
            //variable assigment
            $data = $this->input->post();
            //Type casting            
            $id = intval($data['id']);

            //logic
            if (is_numeric($id) && $id > 0) {
                $selsettings = $this->settings_model->get_setting_by_id($id);
                if (!empty($selsettings)) {
                    $res = $this->settings_model->delete_settings($id);
                    if ($res) {
                        //generate settings dynamically
                        $this->generate_settings();

                        $message = $this->theme->message(lang('setting-message-delete'), 'success');
                    }
                } else {
                    $message = $this->theme->message(lang('setting-message-norec'), 'error');
                }
            }
        } else {
            $message = $this->theme->message(lang('setting-message-norec'), 'error');
        }

        //send message
        echo $message;
    }

    /**
     * generate_settings() for adding constant in custom config file
     * @return boolean
     */
    private function generate_settings() {
        $settings = $this->settings_model->get_settings();
        $custom_configpath = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'custom_config' . EXT;
        $string = '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\'); ' . PHP_EOL;
        foreach ($settings as $val):
            //take alias from an array
            $alias = end(array_keys($val));

            $string .= '/*' . htmlentities($val[$alias]['comment']) . ' */' . PHP_EOL;
            $string .= ' define(\'' . $val[$alias]['setting_label'] . '\',\'' . $val[$alias]['setting_value'] . '\');' . PHP_EOL;
        endforeach;
        $string .= '?>';


        if (@chmod($custom_configpath, 0755)) {
            //echo 'Folder permissions changed';
            // return true;
        } else {
            return false;
        }

        file_put_contents($custom_configpath, $string);
        return true;
    }

    public function view_data($id = 0) {
        $data = array();
        $data = array('data' => $this->settings_model->get_setting_by_id($id));
        $this->breadcrumb->add(lang('view-setting-data'));
        $this->theme->view($data);
    }

}
