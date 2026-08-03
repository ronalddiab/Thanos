<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Languages Admin Controller
 *
 *  Languages for an application
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Languages_admin extends Base_Admin_Controller {

    /**
     * create an instance
     */
    function __construct() {
        parent::__construct();
        //Logic
        $this->access_control($this->access_rules());

        //view variable assignment
        $this->breadcrumb->add('Languages', base_url() .$this->section_name.'/languages');
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('index', 'action', 'delete', 'save', 'view_data', 'default_save'),
                'users' => array('@'),
            )
        );
    }

    /**
     * for geting language list
     */
    function index() {
        //variable assignment
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->languages_model->record_per_page = $this->record_per_page;
        $this->languages_model->offset = $offset;

        if (!empty($this->languages_model->offset)) {
            $this->session->set_custom_userdata($this->section_name, "languages_offset", $this->languages_model->offset);
        }
        if (!empty($this->languages_model->offset)) {
            $this->session->set_custom_userdata($this->section_name, "languages_page_number", $this->page_number);
        }

        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            if(empty($data['page_number']) || $data['page_number'] == 1)
            {
                $this->session->set_custom_userdata($this->section_name, "languages_offset", "");
                $this->session->set_custom_userdata($this->section_name, "languages_page_number", "");
            }

            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->languages_model->sort_by = $data['sort_by'];
                $this->languages_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "languages_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "languages_sort_order", $this->input->post('sort_order'));
            }
            else {
                $this->session->set_custom_userdata($this->section_name, "languages_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "languages_sort_order", "");
            }
        }


        if (!empty($this->session->userdata[$this->section_name]['languages_sort_by'])) {
            $this->languages_model->sort_by = $this->session->userdata[$this->section_name]['languages_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['languages_sort_order'])) {
            $this->languages_model->sort_order = $this->session->userdata[$this->section_name]['languages_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['languages_offset'])) {
            $this->languages_model->offset = $this->session->userdata[$this->section_name]['languages_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['languages_page_number'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['languages_page_number'];
        }

        // load data for url listing
        $languages = $this->languages_model->get_languages_list();

        // Pass data to view file
        $data = array(
            'languages' => $languages,
            'page_number' => $this->page_number,
            'total_records' => $this->languages_model->record_count(),
            'sort_by' => $this->languages_model->sort_by,
            'sort_order' => $this->languages_model->sort_order
        );

        //Create page-title
        $this->theme->set('page_title', lang('languages'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * To redirect add/edit action and show their view
     * @param string $action
     * @param integer $id
     */
    function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting / Exception
            $id = intval($id);
            $action = strip_tags($action);
            custom_filter_input('integer', $id);

            //initialization
            $language_code = "";
            $language_name = "";
            $direction = "";
            $default = "";
            //$status = "0";
            $status = "1";


            //logic
            switch ($action) {
                case 'add':break;
                case 'edit':
                    $data = array();
                    if (is_numeric($id) && $id > 0) {
                        $sellanguages = $this->languages_model->get_languages_by_id($id);
                        if (!empty($sellanguages)) {
                            $language_arr = $sellanguages[0];
                            //take alias from an array
                            $alias = end(array_keys($language_arr));

                            $language_code = $language_arr[$alias]['language_code'];
                            $language_name = $language_arr[$alias]['language_name'];
                            $direction = $language_arr[$alias]['direction'];
                            $default = $language_arr[$alias]['default'];
                            $status = $language_arr[$alias]['status'];
                        } else {
                            redirect($this->section_name.'/languages');
                        }
                    }
                    break;
                default :
                    $this->theme->set_message('This action is not allowed', 'error');
                    redirect($this->section_name.'/languages');
                    break;
            }

            // Pass data to view file
            $data = array(
                'id' => $id,
                'language_code' => $language_code,
                'language_name' => $language_name,
                'direction' => $direction,
                'default' => $default,
                'status' => $status
            );

            //create breadcrumbs
            if ($action == 'add') {
                $this->breadcrumb->add(lang('languages-add'));
                $this->theme->set('page_title', lang('languages-add'));
            } else {
                $this->breadcrumb->add(lang('languages-edit'));
                $this->theme->set('page_title', lang('languages-edit'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect($this->section_name.'/users');
            exit;
        }
    }

    /**
     * save action use for save languages detail
     */
    function save() {


        //set form validation to check server side validation
        $this->load->library('form_validation');
        $data = $this->input->post();

        //Type Casting
        $id = isset($data['id']) ? intval($data['id']) : 0;

        //variable assignment
        $language_code = isset($data['language_code']) ? trim(strip_tags($data['language_code'])) : '';
        $language_name = isset($data['language_name']) ? trim(strip_tags($data['language_name'])) : '';
        $direction = isset($data['direction']) ? trim(strip_tags($data['direction'])) : '';
        $status = isset($data['status']) ? $data['status'] : '';
        $default = isset($data['default']) ? 1 : 0;


        if ($this->input->post('saveLanguages')) {



            //set serverside validation
//            if ($status != 1 && $default == 1) {
//                $this->form_validation->set_rules('default', 'Default', 'checkdefault');
//            }


            $this->form_validation->set_rules('language_code', 'Language Code', 'trim|required|is_unique[languages.language_code.id.' . $id . ']|xss-clean');
            if ($id == 0) {
                $this->form_validation->set_rules('language_name', 'Language Name', 'trim|required|is_unique[languages.language_name.id.' . $id . ']|xss-clean');
            }
            //logic
            if ($this->form_validation->run()) {
                $dataVal = array(
                    'id' => $id,
                    'language_code' => $language_code,
                    'direction' => $direction,
                    'default' => $default,
                    'status' => $status
                );

                //do not update language name
                if ($id == 0) {
                    $dataVal['language_name'] = $language_name;
                }



                if ($dataVal['default'] == 1 && $dataVal['status'] == 0) {

                    $this->theme->set_message(lang('default-cant-update'), 'error');
                    redirect(site_url().$this->section_name.'/languages/action/edit/'.$id);
                } else {
                    $this->languages_model->save_languages($dataVal);

                    if ($id == 0) {
                        //after add crate language directories in  core_modules and modules part.
                        $this->create_lang_directories(strtolower($language_name));
                        $this->theme->set_message(lang('languages-message-add'), 'success');
                    } else {
                        $this->theme->set_message(lang('languages-message-update'), 'success');
                    }

                    //redirect user to listing page
                    redirect($this->section_name.'/languages');
                }
            }
        }

        //set breadcrumb variables
        if ($id == 0 || $id == '') {
            $this->theme->set('page_title', lang('languages-add'));
            $this->breadcrumb->add(lang('languages-add'));
        } else {
            $this->theme->set('page_title', lang('languages-edit'));
            $this->breadcrumb->add(lang('languages-edit'));
        }

        //pass variable
        $data = array(
            'id' => $id,
            'language_code' => $language_code,
            'language_name' => $language_name,
            'direction' => $direction,
            'default' => $default,
            'status' => $status
        );
        //render view if any error
        $this->theme->view($data, 'admin_add');
    }

    function default_save() {

        $data = $this->input->post();

        $id = $data['chk_default'];

//        echo "<pre>";
//        print_r($data);
//        exit;

        if ($this->input->post('mysubmit')) {

            $this->db->where("id != ", $id);
            $this->db->update(TBL_LANGUAGES, array('default' => 0));

            $this->db->where("id = ", $id);
            $this->db->update(TBL_LANGUAGES, array('default' => 1));

            $this->theme->set_message(lang('languages-default-update'), 'success');
            redirect(site_url() . $this->section_name.'/languages');
        }
    }

    /**
     * delete action for deleting language
     */
    function delete() {
        if ($this->input->post()) {
            $data = $this->input->post();
            //Type casting
            $id = intval($data['id']);

            //logic
            if (is_numeric($id) && $id > 0) {
                $sellanguages = $this->languages_model->get_languages_by_id($id);
                if (!empty($sellanguages)) {
                    try {
                        $res = $this->languages_model->delete_languages($id);
                        if ($res > 0) {
                            $this->theme->set_message(lang('languages-message-delete'), 'success');
                        } else if ($res == 0) {
                            $this->theme->set_message(lang('languages-err-message-default'), 'error');
                        } else {
                            $this->theme->set_message(lang('languages-message-relation'), 'error');
                        }
                    } catch (Exception $e) {
                        $e->getMessage();
                    }
                } else {
                    $this->theme->set_message(lang('languages-message-norec'), 'error');
                }
            }
        } else {
            $this->theme->set_message(lang('languages-message-norec'), 'error');
        }
    }

    /**
     * create language directories
     * @param
     */
    private function create_lang_directories($language) {
        //variable assignment
        $default_language = 'english';

        //Logic
        if ($language == $default_language) {
            return true;
        } else {
            $this->load->helper('translate/languages');
            $this->load->helper('file');
            // Base language files.
            $files = find_lang_files(APPPATH . 'language/' . $default_language . '/');

            $flag = 0;
            if (!is_dir(realpath(APPPATH . 'language/' . $language . '/'))) {
                @mkdir(APPPATH . 'language/' . $language . '/', 0777);
                $flag = 1;
            }

            if ($flag) {
                foreach ($files as &$file) {
                    if (is_dir(realpath(APPPATH . 'language/' . $language . '/'))) {
                        //copy default language files in added language folder
                        copy(APPPATH . 'language/' . $default_language . '/' . $file, APPPATH . 'language/' . $language . '/' . $file);
                    }
                }
            }

            // Module lang files
            $modules = module_list();
            $custom_modules = module_list(TRUE);
            foreach ($modules as $module) {
                $module_langs = module_files($module, 'language');
                if (isset($module_langs[$module]['language'][$default_language])) {

                    $source_modulelang_path = implode('/', array($module, 'language', $default_language));
                    $modulelang_path = implode('/', array($module, 'language', $language));
                    if (in_array($module, $custom_modules)) {
                        //copy developer module files into module's language folder
                        $destination_path = APPPATH . 'modules/' . $modulelang_path . '/';
                        if (!is_dir(realpath($destination_path))) {
                            @mkdir($destination_path, 0777);
                            $files = find_lang_files(realpath(APPPATH . 'modules/' . $source_modulelang_path) . '/');
                            if (isset($files[0])) {
                                $sourcefile = realpath(APPPATH . 'modules/' . $source_modulelang_path) . '/' . $files[0];
                                $destinationfile = realpath($destination_path) . '/' . $files[0];
                                //copy language files in added language folder
                                copy($sourcefile, $destinationfile);
                            }
                        }
                    } else {
                        //copy core files into core_modules's language folder
                        $destination_path = FCPATH . 'core_modules/' . $modulelang_path . '/';
                        if (!is_dir($destination_path)) {
                            @mkdir($destination_path, 0777);
                            $files = find_lang_files(FCPATH . 'core_modules/' . $source_modulelang_path . '/');
                            if (isset($files[0])) {
                                $sourcefile = FCPATH . 'core_modules/' . $source_modulelang_path . '/' . $files[0];
                                $destinationfile = $destination_path . '/' . $files[0];
                                //copy language files in added language folder
                                copy($sourcefile, $destinationfile);
                            }
                        }
                    }
                }//end if
            }//end foreach
            return true;
        }
    }

    public function view_data($id = 0) {
        $data = array();
        $data = array('data' => $this->languages_model->get_languages_by_id($id));
        $this->theme->view($data);
    }

}