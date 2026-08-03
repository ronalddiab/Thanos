<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Menu Admin Controller
 *
 *  Menu admin controller to manage menu navigation.
 *  Also can add new menu for manage different link on diffenet section with language wise.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Menu_admin extends Base_Admin_Controller {

    protected $_languages;

    /*
     * Create an instance
     */

    function __construct() {
        parent::__construct();

        //Logic
        $this->access_control($this->access_rules());

        //load language model
        $this->load->model('languages/languages_model');
        $this->_languages = $this->languages_model->getalllanguages();

        //set breadcrum heading
        $this->breadcrumb->add('Menu', base_url() . $this->section_name . '/menu');
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('index', 'action', 'delete', 'save', 'get_menulist', 'get_subpages', 'testcurl'),
                'users' => array('@'),
            )
        );
    }

    /**
     * action to display list of menu langauge wise
     * @param integer $lang
     */
    function index($lang = "") {

        //variable assignment
        $data = $this->input->post();
        if (isset($data['lang_id'])) {
            $lang_id = intval(urldecode($data['lang_id']));
            $lang_code = $this->_languages[$lang_id];
        } else {
            if (trim($lang) != "") {
                $languages = array_flip($this->_languages);
                if (isset($languages[$lang])) {
                    $lang_id = intval($languages[$lang]);
                }
            } else {
                $lang_id = $this->session->userdata[$this->section_name]['site_lang_id'];
            }
        }

        //set languageid in menu model to retrive language wise data
        $this->menu_model->language_id = $lang_id;


        //pass data to view
        $data['menu'] = $this->menu_model->get_menu_list(0, 0);
        $data['languages'] = $this->languages_model->get_languages(0, 1);
        $data['lang_id'] = $lang_id;

        //view variable assignment
        $this->theme->set('page_title', lang('menu'));
        $this->theme->view($data);
    }

    /**
     * action to display list of menu langauge wise - using ajax call
     * @param string $action default = 'add'
     * @param string $lang defailt = ''
     * @param integer $id default = 0
     */
    function action($action = "add", $lang = '', $id = 0) {
        if ($this->check_permission()) {
            //Type Casting / Exception
            $id = intval($id);
            $action = strip_tags($action);
            $lang = trim(strip_tags($lang));
            custom_filter_input('integer', $id);

            //variable assignment
            if ($lang != '') {
                $languages = array_flip($this->_languages);
                if (isset($languages[$lang])) {
                    $lang_id = $languages[$lang];
                    $lang_code = $lang;
                }
            } else {
                $lang_id = $this->session->userdata[$this->section_name]['site_lang_id'];
                $lang_code = $this->session->userdata[$this->section_name]['site_lang_code'];
            }

            //variable assignment
            $this->menu_model->language_id = $lang_id;
            $menu_name = 'add-menu';
            $menu_title = "";
            $menu_link = "";
            $parent_id = 0;
            $status = "";
            $modules = array('' => lang('menu-select')); //get modules list
            $menu_items = array(0 => lang('menu-root'));
            $menu_names = array();
            $module_name = "";


            switch ($action) {
                case 'add':
                    // echo "Here"; exit;

                    //get all active menu items
                    $getMenuList = $this->menu_model->get_menu_list(0, 0, array('m.menu_name' => 'admin_menu', 'm.status' => 1));
                    if (!empty($getMenuList)) {
                        if (isset($getMenuList[$menu_name])) {
                            foreach ($getMenuList[$menu_name] as $key => $val) {
                                $menu_items[$val['id']] = $val['title'];
                            }
                        }
                    }
                    $modulelist = getmodulelist();
                    $modules = array_merge($modules, $modulelist);
                    $menu_names = $this->menu_model->get_menu_name();
                    $menu_section_val = '';

                    break;
                case 'edit':
                    //Check whether record exist or not?
                    $dataArr = $this->menu_model->get_menu_detail(array('m.id' => $id));

//                    echo '<pre>';
//                    print_r($dataArr);
//                    exit;

                    if (!empty($dataArr)) {
                        //variable assignment for edit view
                        $menu_name = $dataArr[0]['m']['menu_name'];
                        $menu_title = $dataArr[0]['m']['title'];
                        $menu_link = $dataArr[0]['m']['link'];

                        if (strpos($menu_link, 'admin/') !== false) {
                            $menu_link = str_replace('admin/', '', $menu_link);

                            //  $menu_section_arr = array('admin');
                            $menu_section_val = 'admin';

                        } else {
                            // $menu_section_arr = array('front');
                            $menu_section_val = 'front';
                        }


                        $parent_id = $dataArr[0]['m']['parent_id'];
                        $lang_id = $dataArr[0]['m']['lang_id'];
                        $status = $dataArr[0]['m']['status'];

                        //get all active menu items
                        $getMenuList = $this->menu_model->get_menu_list(0, 0, array('m.menu_name' => $menu_name, 'm.status' => 1));
                        if (!empty($getMenuList)) {
                            if (isset($getMenuList[$menu_name])) {
                                foreach ($getMenuList[$menu_name] as $key => $val) {
                                    $menu_items[$val['id']] = $val['title'];
                                }
                            }
                        }
                        $modulelist = getmodulelist();
                        $modules = array_merge($modules, $modulelist);
                        $menu_names = $this->menu_model->get_menu_name();
                    } else {
                        //redirect user to list page
                        redirect(get_current_section($this) . '/menu/index/' . $lang_code);
                    }
                    break;
                default :
                    $this->theme->set_message('This action is not allowed', 'error');
                    redirect($this - section_name . '/menu');
                    break;
            }

            // Pass data to view file
            $data = array(
                'id' => $id,
                'menu_name' => $menu_name,
                'title' => $menu_title,
                'link' => $menu_link,
                'menu_section_val' => $menu_section_val,
                'parent_id' => $parent_id,
                'lang_id' => $lang_id,
                'status' => $status,
                'language_name' => $lang_code,
                'modules' => $modules,
                'menu_items' => $menu_items,
                'menu_names' => $menu_names,
                'module_name' => $module_name
            );

            //create breadcrumbs
            if ($action == 'add') {
                $this->theme->set('page_title', lang('menu-add'));
                $this->breadcrumb->add(lang('menu-add'));
            } else {
                $this->theme->set('page_title', lang('menu-edit'));
                $this->breadcrumb->add(lang('menu-edit'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect($this - section_name . '/users');
            exit;
        }
    }

    /**
     * ajax function to get modules action
     */
    public function get_subpages() {
        //variable assignment
        $data = $this->input->post();
        $langid = intval($data['lang_id']);
        $pages = array();
        $module_name = '';

        if (isset($data['module_name'])) {
            //set language id in model
            $this->menu_model->language_id = $langid;
            $langCode = $this->_languages[$langid];
            $this->lang->load($this->_module, $this->_languages[$langCode]);
            $module_name = $data['module_name'];
            switch ($module_name) {
                case 'cms':
                    $pages = $this->menu_model->get_pages($module_name);
                    break;
                default:
                    break;
            }
        }
        //pass data to view
        $dataVal = array(
            'lang_id' => $langid,
            'pages' => $pages,
            'module_name' => $module_name
        );
        //render view
        $this->theme->view($dataVal);
    }

    /**
     * ajax function to get menulist action
     */
    public function get_menulist() {
        //variable assigment
        $data = $this->input->post();
        $langid = intval($data['lang_id']);
        $menulist = array();
        $menu_name = '';

        //Logic
        if (isset($data['menu_name'])) {
            $this->menu_model->language_id = $langid;
            $langCode = $this->_languages[$langid];
            $menu_name = $data['menu_name'];
            $this->lang->load($this->_module, $this->_languages[$langCode]);
            $list_arr = $this->menu_model->get_menu_list(0, 0, array('m.menu_name' => urldecode($menu_name), 'm.status' => 1));
            $menulist = $list_arr[$menu_name];
        }

        //pass data to view
        $dataVal = array(
            'lang_id' => $langid,
            'menulist' => $menulist,
            'menu_name' => $menu_name
        );

        //render view
        $this->theme->view($dataVal);
    }

    function delete() {
        //variable assigment
        $data = $this->input->post();
        $id = intval($data['id']);
        $lang_id = intval($data['lang_id']);
        $this->menu_model->language_id = $lang_id;
        $menuarr = $this->menu_model->get_menu_detail(array('m.id' => $id));
        //Logic
        if (!empty($menuarr)) {
            $res = $this->menu_model->delete_menu($id);
            if ($res == 0) {
                echo $this->theme->message(lang('menu-message-not-delete'), 'error');
            } else {
                echo $this->theme->message(lang('menu-message-delete'), 'success');
            }
        } else {
            echo $this->theme->message(lang('menu-message-norec-delete'), 'error');
        }
    }

    /**
     * action to save menu
     */
    function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');
        $data = $this->input->post();

//        echo '<pre>';
//        print_r($data);
//        exit;


        //variable assignment
        $id = isset($data['id']) ? intval($data['id']) : 0;
        custom_filter_input('integer', $id); //filter id
        $lang_id = isset($data['lang_id']) ? intval($data['lang_id']) : $this->session->userdata[$this->section_name]['site_lang_id'];
        custom_filter_input('integer', $lang_id); //filter id
        $module = array('' => lang('menu-select')); //get modules list
        $modulelist = getmodulelist();
        $modules = array_merge($module, $modulelist);
        $title = isset($data['title']) ? $data['title'] : '';
        $module_name = isset($data['module_name']) ? $data['module_name'] : '';
        $link = isset($data['link']) ? $data['link'] : '';
        $parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
        $status = isset($data['status']) ? $data['status'] : 0;
        $lang_code = isset($data['language_name']) ? $data['language_name'] : $this->session->userdata[$this->section_name]['site_lang_code'];
        $menu_items = array(0 => lang('menu-root'));

        $this->menu_model->language_id = $lang_id; //assign languageid in menu model
        $get_menu_list = $this->menu_model->get_menu_list(0, 0); //retrive menu list array
        $menu_name = isset($data['menu_name']) ? trim(($data['menu_name'] == 'add-menu') ? $data['new_menu'] : $data['menu_name']) : 'add-menu';

        //assign list to menu item array
        if (!empty($get_menu_list)) {
            if (isset($get_menu_list[$menu_name])) {
                foreach ($get_menu_list[$menu_name] as $key => $val) {
                    $menu_items[$val['id']] = $val['title'];
                }
            }
        }
        $menu_names = $this->menu_model->get_menu_name();


        if ($this->input->post('savemenu')) {
            //Logic - Server-side- validation
            if (($data['menu_name'] == 'add-menu')) {
                $this->form_validation->set_rules('new_menu', 'Menu Name', 'trim|required');
            } else {
                $this->form_validation->set_rules('menu_name', 'Menu Name', 'trim|required');
            }

            if (isset($id) && $id != 0 && $parent_id != 0) {
                $Ids = $this->menu_model->get_child_menu_array($id);

                if (!empty($Ids) && in_array($parent_id, $Ids, true)) {
                    $this->form_validation->set_rules('parent_id', 'Parent', 'child_menu');
                }
                if ($id == $parent_id) {
                    $parent_id = 0;
                }
            }
            if ($status == 0) {
                $this->form_validation->set_rules('status', 'Status', 'trim|required|callback_check_active_sub_menu');
            }
            $this->form_validation->set_rules('title', 'Title', 'trim|required|callback_check_unique_title');
            $this->form_validation->set_rules('link', 'Link', 'trim|required|urlexist[link]');

            if ($this->form_validation->run($this)) {
                $link = (end($link) != '/') ? $link : '';

                if ($menu_name == 'admin_menu') {
                    $link = $data['menu_section'] . '/' . $link;
                }

                //echo $menu_name." -- ".$link;exit;
                //set default variables
                $dataVal = array(
                    'id' => $id,
                    'menu_name' => $menu_name,
                    'title' => $title,
                    'link' => $link,
                    'parent_id' => $parent_id,
                    'lang_id' => $lang_id,
                    'status' => $status
                );

                //save menu array
                $this->menu_model->save_menu($dataVal);
                if ($id == 0) {
                    $this->theme->set_message(lang('menu-message-add'), 'success');
                } else {
                    $this->theme->set_message(lang('menu-message-update'), 'success');
                }
                redirect($this->section_name . '/menu/index/' . $lang_code);
            }
        }
        //Pass data to view file
        $data['id'] = $id;
        $data['menu_name'] = $menu_name;
        $data['title'] = $title;
        $data['link'] = $link;
        $data['parent_id'] = $parent_id;
        $data['lang_id'] = $lang_id;
        $data['status'] = $status;
        $data['language_name'] = $lang_code;
        $data['modules'] = $modules;
        $data['menu_items'] = $menu_items;
        $data['menu_names'] = $menu_names;
        $data['module_name'] = $module_name;
        $data['menu_section_val'] = $data['menu_section'];

        //create breadcrumbs
        if (is_numeric($id) && $id == 0 || $id == '') {
            $this->theme->set('page_title', lang('menu-add'));
            $this->breadcrumb->add(lang('menu-add'));
        } else {
            $this->theme->set('page_title', lang('menu-edit'));
            $this->breadcrumb->add(lang('menu-edit'));
        }
        $this->theme->view($data, 'admin_add');
    }

    /*
     * function for check unique title
     * callback_check_unique_title
     */

    public function check_unique_title() {
        $data = $this->input->post();
        $result = $this->menu_model->check_unique_fields($data, 'title');
        if ($result > 0) {
            $this->form_validation->set_message('check_unique_title', lang('msg-unique-title'));
            return false;
        } else {
            return true;
        }
    }

    /*
     * function check_active_sub_menu to check active submenu
     * callback_check_unique_title
     */

    public function check_active_sub_menu() {
        $data = $this->input->post();

        $result = $this->menu_model->get_active_sub_menu($data['id'], $data['lang_id']);
        if (!empty($result)) {
            $this->form_validation->set_message('check_active_sub_menu', lang('msg-inactive-sub-menu'));
            return false;
        } else {
            return true;
        }
    }

}
