<?php

/**
 *  URL Admin Controller
 *
 *  To perform url management.
 *
 * @package CIDemoApplication
 * @subpackage Urls
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Urls_admin extends Base_Admin_Controller {

    private $admin_module = array('languages', 'menu', 'permissions', 'roles', 'settings', 'translate', 'urls', 'users', '.svn');

    /**
     * Function index to view listing of Urls
     */
    function __construct() {
        parent::__construct();

        //Logic
        $this->access_control($this->access_rules());

        $this->breadcrumb->add(lang('url-management'), base_url() . $this->section_name . '/urls');
        $this->load->library('form_validation');
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules() {
        return array(
            array(
                'actions' => array('index', 'action', 'delete', 'save', 'get_related', 'view_data'),
                'users' => array('@'),
            )
        );
    }

    /**
     * Function index to view listing of Urls [Using Post method]
     */
    function index() {


        //Variable Assignment
        $dir = APPPATH . '/modules';
        $dirlist = opendir($dir);
        while ($file = readdir($dirlist)) {
            if ($file != '.' && $file != '..' && $file != '.DS_Store') {
                $modules_array[$file] = $file;
            }
        }
        $modules = array_diff($modules_array, $this->admin_module);
        $modules_list = $modules;




        //variable assignment
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->urls_model->record_per_page = $this->record_per_page;
        $this->urls_model->offset = $offset;

        if (!empty($this->urls_model->offset)) {
            $this->session->set_custom_userdata($this->section_name, "url_offset", $this->urls_model->offset);
        }
        if (!empty($this->urls_model->offset)) {
            $this->session->set_custom_userdata($this->section_name, "url_page_number", $this->page_number);
        }



        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            //  print_r($data);exit;

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "url_offset", "");
                $this->session->set_custom_userdata($this->section_name, "url_page_number", "");
            }

            if (isset($data['search_term']) && !empty($data['search_term'])) {
                $this->urls_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "url_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "url_search_term", "");
            }

            if (isset($data['search']) && !empty($data['search'])) {
                $this->urls_model->search = $data['search'];
                $this->session->set_custom_userdata($this->section_name, "url_search", $this->input->post('search'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "url_search", "");
            }

            if (isset($data['module']) && !empty($data['module'])) {
                $this->urls_model->module = $data['module'];
                $this->session->set_custom_userdata($this->section_name, "url_module", $this->input->post('module'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "url_module", "");
            }


            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->urls_model->sort_by = $data['sort_by'];
                $this->urls_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "url_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "url_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "url_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "url_sort_order", "");
            }

            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->urls_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('url-delete-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->urls_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('url-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->urls_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('url-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->urls_model->active_all_records()) {
                    echo $this->theme->message(lang('url-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->urls_model->inactive_all_records()) {
                    echo $this->theme->message(lang('url-inactive-success'), 'success');
                    exit;
                }
            }
        }


        if (!empty($this->session->userdata[$this->section_name]['url_search_term'])) {
            $this->urls_model->search_term = trim($this->session->userdata[$this->section_name]['url_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['url_search'])) {
            $this->urls_model->search = $this->session->userdata[$this->section_name]['url_search'];
        }
        if (!empty($this->session->userdata[$this->section_name]['url_module'])) {
            $this->urls_model->module = trim($this->session->userdata[$this->section_name]['url_module']);
        }


        if (!empty($this->session->userdata[$this->section_name]['url_sort_by'])) {
            $this->urls_model->sort_by = $this->session->userdata[$this->section_name]['url_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['url_sort_order'])) {
            $this->urls_model->sort_order = $this->session->userdata[$this->section_name]['url_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['url_offset'])) {
            $this->urls_model->offset = $this->session->userdata[$this->section_name]['url_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['url_page_number'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['url_page_number'];
        }




        //get url listing records
        $urls = $this->urls_model->get_url_listing();
        $this->urls_model->_record_count = true;
        $total_records = $this->urls_model->get_url_listing();
        // Pass data to view file
        $data = array(
            'urls' => $urls,
            'page_number' => $this->page_number,
            'total_records' => $total_records,
            'search_term' => $this->urls_model->search_term,
            'search' => $this->urls_model->search,
            'module' => $this->urls_model->module,
            'sort_by' => $this->urls_model->sort_by,
            'sort_order' => $this->urls_model->sort_order,
            'modules_list' => $modules_list
        );

        //Create page-title
        $this->theme->set('page_title', lang('url-management'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * Function action to perform insert & update by action parameter
     * @param string $action default = 'add'
     * @param integer $id default = 0
     */
    function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            custom_filter_input('integer', $id);

            //Variable Assignment
            $dir = APPPATH . '/modules';
            $dirlist = opendir($dir);
            $modules_array[''] = 'Select';
            while ($file = readdir($dirlist)) {
                if ($file != '.' && $file != '..' && $file != '.DS_Store') {
                    $modules_array[$file] = $file;
                }
            }
            $modules = array_diff($modules_array, $this->admin_module);
            $modules_list = $modules;
            $related[''] = 'Select';
            $related_list = $related;

            $module_name = "";
            $related_id = "";
            $core_url = "";
            $slug_url = "";
            $order = "";
            $status = "";

            //Logic
            switch ($action) {
                case 'add':

                    break;
                case 'edit':

                    $result = $this->urls_model->get_url_by_id($id);
                    if (!empty($result)) {
                        //Variable assignment for edit view
                        $module_name = $result[0]['u']['module_name'];
                        $related_id = $result[0]['u']['related_id'];
                        $core_url = $result[0]['u']['core_url'];
                        $slug_url = $result[0]['u']['slug_url'];
                        $order = $result[0]['u']['order'];
                        $status = $result[0]['u']['status'];
                    } else {
                        $this->theme->set_message(lang('invalid-id-msg'), 'error');
                        //If urls not exist then redirecting to listing page
                        redirect($this->section_name . '/urls');
                    }

                    break;
                default :
                    $this->theme->set_message(lang('action-not-allowed'), 'error');
                    redirect($this->section_name . '/urls');
                    break;
            }

            // Pass data to view file
            $data = array(
                'id' => $id,
                'modules_list' => $modules_list,
                'related_list' => $related_list,
                'module_name' => $module_name,
                'related_id' => $related_id,
                'core_url' => $core_url,
                'slug_url' => $slug_url,
                'order' => $order,
                'status' => $status
            );

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add-url'));
                $this->breadcrumb->add(lang('add-url'));
            } else {
                $this->theme->set('page_title', lang('edit-url'));
                $this->breadcrumb->add(lang('edit-url'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect($this->section_name . '/users');
            exit;
        }
    }

    /**
     * Function save to insert/update user data
     */
    function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();

            //Type Casting
            $id = intval($data['id']);
            $related_id = (isset($data['related_id']) ? $data['related_id'] : 0);
            //Variable Assignment
            $slug_url = trim(strip_tags($data['slug_url']));
            $module_name = trim(strip_tags($data['module_name']));
            $related_id = intval($related_id);
            $core_url = trim(strip_tags($data['core_url']));
            $order = trim(strip_tags($data['order']));
            $status = $data['status'];

            //Validation rules for URL
            $this->form_validation->set_rules('slug_url', lang('slug-url'), 'required|is_unique[url_management.slug_url.id.' . $id . ']');
            $this->form_validation->set_rules('core_url', lang('core-url'), 'trim|required|url_exist[core_url.' . $module_name . ']');

            if ($this->form_validation->run()) {
                $data_array['id'] = $id;
                $data_array['slug_url'] = $slug_url;
                $data_array['module_name'] = $module_name;
                $data_array['related_id'] = $related_id;
                $data_array['core_url'] = $core_url;
                $data_array['order'] = $order;
                $data_array['status'] = $status;

                if ($module_name == 'cms') {
                    $this->load->model('cms/cms_model');
                    $this->cms_model->update_slug($related_id, $slug_url);
                }
                $lastId = $this->urls_model->save_url($data_array);
                $this->generate_custom_url();

                if ($id == 0) {
                    $this->theme->set_message(lang('url-add-success'), 'success');
                } else {
                    $this->theme->set_message(lang('url-edit-success'), 'success');
                }

                redirect($this->section_name . '/urls');
                exit;
            }
        } else {
            $id = 0;
            $slug_url = "";
            $module_name = "";
            $related_id = "";
            $core_url = "";
            $order = "";
            $status = "";
        }

        $dir = APPPATH . '/modules';
        $dirlist = opendir($dir);
        $modules_array[''] = 'Select';
        while ($file = readdir($dirlist)) {
            if ($file != '.' && $file != '..' && $file != '.DS_Store') {
                $modules_array[$file] = $file;
            }
        }
        $modules = array_diff($modules_array, $this->admin_module);
        $modules_list = $modules;
        $related[''] = 'Select';
        $related_list = $related;

        // Pass data to view file
        $data['id'] = $id;
        $data['modules_list'] = $modules_list;
        $data['related_list'] = $related_list;
        $data['module_name'] = $module_name;
        $data['related_id'] = $related_id;
        $data['core_url'] = $core_url;
        $data['slug_url'] = $slug_url;
        $data['order'] = $order;
        $data['status'] = $status;

        //create breadcrumbs & page-title
        if ($id == 0 && $id != '') {
            $status = 1;
            $this->theme->set('page_title', lang('add-url'));
            $this->breadcrumb->add(lang('add-url'));
        } else {
            $status = $data['status'];
            $this->theme->set('page_title', lang('edit-url'));
            $this->breadcrumb->add(lang('edit-url'));
        }
        //Render view
        $this->theme->view($data, 'admin_add');
    }

    /**
     * Function delete to URL (Ajax-Post)
     */
    function delete() {
        //Initializing
        $data = $this->input->post();

        //Type-casting
        $id = intval($data['id']);

        //Logic
        $resdata = $this->urls_model->get_url_by_id($id);
        if (!empty($resdata)) {
            $res = $this->urls_model->delete_url($id);
            if ($res) {
                $message = $this->theme->message(lang('url-delete-success'), 'success');
            }
        } else {
            $message = $this->theme->message(lang('invalid-id-msg'), 'error');
        }

        //message
        echo $message;
    }

    /**
     * Function get_related to get cms pages(Ajax)
     */
    public function get_related() {
        //Type casting
        $table_name = trim(strip_tags($this->input->post('module_name')));
        $data = array();

        //Logic
        if ($this->db->table_exists($table_name) && $table_name == 'cms') {
            $data['pages'] = $this->urls_model->get_records($table_name);
        }

        $this->theme->view($data);
    }

    /*
      public function generate_custom_url() {
      $urls = $this->urls_model->get_url_listing();

      $custom_configpath = APPPATH . 'config' . "/" . 'custom_routes' . EXT;

      $string = "";
      $string = '<?php  ' . PHP_EOL;

      foreach ($urls as $val):

      $language_detail = $this->languages_model->get_languages_by_id($val['u']['language_id']);

      $string .= ' $route[' . "\"" . ($language_detail[0]['l']['language_code'] . '/' . $val['u']['slug_url']) . "\"" . '] = ' . "\"" . $val['u']['module_name'] . '/' . $val['u']['core_url'] . "\"" . ';' . PHP_EOL;

      endforeach;
      $string .= '?>';


      @chmod($custom_configpath, 0777);
      file_put_contents($custom_configpath, $string);
      return true;
      } */

    public function generate_custom_url() {


        global $CFG; // define global config var

        $urls = $this->urls_model->get_url_listing();

        $custom_configpath = APPPATH . 'config' . "/" . 'custom_routes' . EXT;

        $string = "";
        $string = '<?php  ' . PHP_EOL;

        foreach ($urls as $val):

            // Check for Multi Languages Option

            if ($CFG->item('multilang_option') == 1) {
                $language_detail = $this->languages_model->get_languages_by_id($val['u']['language_id']);

                $string .= ' $route[' . "\"" . ($language_detail[0]['l']['language_code'] . '/' . $val['u']['slug_url']) . "\"" . '] = ' . "\"" . $val['u']['module_name'] . '/' . $val['u']['core_url'] . "\"" . ';' . PHP_EOL;
            } else if ($CFG->item('multilang_option') == 0) {


                $string .= ' $route[' . "\"" . ($val['u']['slug_url']) . "\"" . '] = ' . "\"" . $val['u']['module_name'] . '/' . $val['u']['core_url'] . "\"" . ';' . PHP_EOL;
            }
        endforeach;
        $string .= '?>';


        @chmod($custom_configpath, 0777);
        file_put_contents($custom_configpath, $string);
        return true;
    }

    public function view_data($id = 0) {
        $data = array();
        $data = array('data' => $this->urls_model->get_url_by_id($id));
        $this->theme->view($data);
    }

}
