<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Certification_menu_admin extends Base_Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->breadcrumb->add(lang('certification-menu-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'certification_menu');
        $this->access_control($this->access_rules());
        $this->language = $this->uri->segment(4);
        $this->load->library('unit_test');
        $this->load->model('certification_menu_model');    
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {

        return array(
            array(
                'actions' => array('index','action','save','submenus','delete','view_data'),
                'users' => array('@'),
            )
        );
    }
    /**
     * Function index to view listing of certification_menus
     */
    function index() {
        $data = array();
        //Paging parameters
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->certification_menu_model->record_per_page = $this->record_per_page;
        $this->certification_menu_model->offset = $offset;


        //set sort/search parameters in pagging
        if ($this->input->post()) {

            $data = $this->input->post();
            // Search Term ***
            if (isset($data['search_term']) && !empty($data['search_term'])) {
                $this->certification_menu_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "certification_menu_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "certification_menu_search_term", "");
            }
            // Search Term ***
            // Sort Order ***
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->certification_menu_model->sort_by = $data['sort_by'];
                $this->certification_menu_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "certification_menu_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "certification_menu_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "certification_menu_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "certification_menu_sort_order", "");
            }
            // Sort Order ***


            if (isset($data['type']) && $data['type'] == 'delete') {

                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = $val;
                }
                // Newly added
                if ($this->certification_menu_model->delete_section($tempArr)) {
                    echo $this->theme->message(lang('certification_menu-delete-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('certification_menu-contains-site-error'), 'error');
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
                if ($this->certification_menu_model->active_records($tempArr)) {
                    echo $this->theme->message(lang('certification_menu-active-success'), 'success');
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
                if ($this->certification_menu_model->inactive_records($tempArr)) {
                    echo $this->theme->message(lang('certification_menu-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('certification_menu-contains-site-error'), 'error');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->certification_menu_model->active_all_records()) {
                    echo $this->theme->message(lang('certification_menu-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->certification_menu_model->inactive_all_records()) {
                    echo $this->theme->message(lang('certification_menu-inactive-success'), 'success');
                    exit;
                }else{
                    echo $this->theme->message(lang('certification_menu-contains-site-error'), 'error');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['certification_menu_search_term'])) {
            $this->certification_menu_model->search_term = trim($this->session->userdata[$this->section_name]['certification_menu_search_term']);
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['certification_menu_sort_by'])) {
            $this->certification_menu_model->sort_by = $this->session->userdata[$this->section_name]['certification_menu_sort_by'];
        }
        if (!empty($this->session->userdata[BASE_ADMIN_URL_CUSTOM]['certification_menu_sort_order'])) {
            $this->certification_menu_model->sort_order = $this->session->userdata[$this->section_name]['certification_menu_sort_order'];
        }

        //Load data for url listing
        $menus = $this->certification_menu_model->get_certification_menu_listing();
        $this->certification_menu_model->_record_count = true;
        $total_records = $this->certification_menu_model->get_certification_menu_listing();
        // Pass data to view file
        $this->search_term = $this->certification_menu_model->search_term;
        $data['menus'] = $menus;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->certification_menu_model->search_term;
        $data['sort_by'] = $this->certification_menu_model->sort_by;
        $data['sort_order'] = $this->certification_menu_model->sort_order;
        //Create page-title
        $this->theme->set('page_title', lang('certification-menu-management'));
        //Render view
        $this->theme->view($data);
    }

    function delete() {
        if ($this->check_permission()) {
            $data = $this->input->post();
            $id = intval(base64_decode($data['id']));

            $result = $this->certification_menu_model->get_section_detail($id);

            if (!empty($result)) {
                $res = $this->certification_menu_model->delete_section($id);
                if ($res) {
                    echo $this->theme->message(lang('certification_menu-delete-success'), 'success');
                }
            } else {
                echo $this->theme->message(lang('invalid-id-msg'), 'error');
            }
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'certification_menus');
            exit;
        }
    }
    /**
     * Function menus_validation_rules to validate input
     */
    function certification_menu_validation_rules() {
        $id = intval($this->input->post('id'));
        $this->form_validation->set_rules('title', lang('title'), 'trim|required|min_length[2]|max_length[20]');
    }

    function action($action = "add", $id = 0) {
        if ($this->check_permission()) {
            //Type Casting
            $id = intval($id);
            $action = trim(strip_tags($action));
            $type = custom_filter_input('integer', $id);

            //Variable Assignment
            $title = "";
            $status = "";
            $section_description = '';
            $parent_id = 0;
            $main_menu_id = 0;
            $slug='';
            $is_upload_checked = 0;
            $section_logo = '';
            //get default menus 
            $certification_main_menu =  $this->certification_menu_model->get_certification_parent_menu();
            $certification_menus = $this->certification_menu_model->get_certification_menu($certification_main_menu);
            $questions = array();
            switch ($action) {
                case 'add':
                    break;
                case 'edit':
                    $result = $this->certification_menu_model->get_section_detail($id);
                    $questions = $this->certification_menu_model->get_section_questions($id);
                    $data['questions'] = $questions;
                    $id = $data['id'] = $result['id'];
                    if (!empty($result)) {
                        $title = $result['title'];
                        $status = isset($result['status']) ? $result['status'] : 1;
                        $is_upload_checked = isset($result['is_upload_checked']) ? $result['is_upload_checked'] : 0;
                        $section_logo = $result['section_logo'];
                        $main_menu_id = $result['main_menu_id'];
                        $section_description = $result['section_description'];
                    } else {
                        $this->theme->set_message(lang('certification_menu-not-exist'), 'error');
                        redirect(BASE_ADMIN_URL_CUSTOM . 'certification_menu');
                    }
                    break;
                default :
                $this->theme->set_message(lang('action-not-allowed'), 'error');
                redirect(BASE_ADMIN_URL_CUSTOM. 'certification_menu');
                break;
            }
            // Pass data to view file
            $data['id'] = $id;
            $data['title'] = $title;
            $data['parent_id'] = $parent_id;
            $data['main_menu_id'] = $main_menu_id;
            $data['status'] = $status;
            $data['action'] = $action;
            $data['slug'] = $slug;
            $data['questions'] = $questions;
            $data['certification_menus'] = $certification_menus;
            $data['menu_tree'] = $menu_tree;
            $data['is_upload_checked'] = $is_upload_checked;
            $data['section_description'] = $section_description;
            $data['section_logo'] = $section_logo;

            //create breadcrumbs & page-title
            if ($action == 'add') {
                $this->theme->set('page_title', lang('add-certification_menu'));
                $this->breadcrumb->add(lang('add-certification_menu'));
            } else {
                $this->theme->set('page_title', lang('edit-certification_menu'));
                $this->breadcrumb->add(lang('edit-certification_menu'));
            }

            //Render view
            $this->theme->view($data, 'admin_add');
        }
        else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'certification_menu');
            exit;
        }
    }
    public function view_data($id = 0) {
        $result = $this->certification_menu_model->get_section_detail($id);
        $parent_array = $this->certification_menu_model->getAncestors($id,1);
        $parent_array = array_reverse($parent_array);
        $questions = $this->certification_menu_model->get_section_questions($id);
        $data = array();
        $data= $result;
        $data['questions'] = $questions;
        $data['parent_array'] = $parent_array;
        $this->breadcrumb->add(lang('view-certification_menu'));
        $this->theme->view($data);
    }
    function submenus()
    {
        $data = $this->input->post();
        $result = $this->certification_menu_model->get_section_detail($data['id']);
        $options_html = '';
        $parent_id = $data['parent_id'];
        if($parent_id!='')
        {
        $certification_menus = $this->certification_menu_model->get_certification_menu($parent_id);
        $parent_grp = $this->certification_menu_model->getAncestors($data['id']);
        if(!empty($certification_menus))
        {
            $options_html = generateOptions($certification_menus,$result['id'],$result['parent_id'],$parent_grp);
        }
        }
        echo $options_html;
    }
    function save() {
        //set form validation to check server side validation
        $this->load->library('form_validation');
        //get default menus 
        $certification_main_menu =  $this->certification_menu_model->get_certification_parent_menu();
        $certification_menus = $this->certification_menu_model->get_certification_menu($certification_main_menu);
        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();
            //Type Casting
            $id = intval($data['id']);
            $level = intval($data['level']);
            $parent_id = $data['parent_id'.'_'.$level];
            $main_menu_id = intval($data['main_menu_id']);
            $section_description = trim($data['section_description']);
            $title = trim(strip_tags($data['title']));
            $slug = trim(strip_tags($data['slug']));
            $is_upload_checked = $data['is_upload_checked'] ? 1:0;
            if ($id == 0) {
                $status = $data['status'];
            } else {
                $status = $data['status'];
            }

            // field name, error message, validation rules
            $this->certification_menu_validation_rules();

            if ($this->form_validation->run($this)) {
                $data_array['id'] = $id;
                $data_array['title'] = $title;
                $data_array['status'] = $status;
                $data_array['main_menu_id'] = $main_menu_id;
                $data_array['parent_id'] = $parent_id;
                $data_array['slug']=$slug;
                $data_array['section_description']=$section_description;
                $data_array['is_upload_checked'] = $is_upload_checked;
				if (isset($_FILES['section_logo']['name'])) {

                    $config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
					$config['max_size']       = '2048';
					$config['maintain_ratio'] = true;
					$config['width']          = 140;
					$config['height']         = 100;
					$this->load->library('upload', $config);
					$this->upload->initialize($config);

                    $valid_formats = array("jpg", "png");
					$imagename     = $_FILES['section_logo']['name'];
                    $size = $_FILES['section_logo']['size'];
					$i    = strrpos($imagename, ".");

                    if (!$i) {
						$ext = '';
					}

                    $l              = strlen($imagename) - $i;
					$ext            = substr($imagename, $i + 1, $l);
					$section_logo_name = 'section_logo_' . rand(11111, 9999999) . '.' . $ext;

                    if ($ext) {

						if (in_array($ext, $valid_formats)) {
							// procedure further if and only if image size can not be more than 10MB.

							if ($size < (1024 * 1024 * 10)) {

								$uploadedfile = $_FILES['section_logo']['tmp_name'];

								$target_file  = BASE_PATH_CUSTOM . "/assets/uploads/" . $section_logo_name;

								$_movestatus  = move_uploaded_file($uploadedfile, $target_file);

								if (!$_movestatus) {
									$this->theme->set_message('Section logo is not uploaded', 'error');
								} else {

									$this->load->library('image_lib');

									$config['image_library'] = 'gd2';

									$config['source_image']  = $target_file;

									$this->image_lib->clear();

									$this->image_lib->initialize($config);

									if (!$this->image_lib->resize()) {
										echo $this->image_lib->display_errors();
									}

									$section_logo               = trim(strip_tags($section_logo_name));
									$data_array['section_logo'] = $section_logo;

									// Delete Old file
									// $oldfile = BASE_PATH_CUSTOM . "/assets/uploads/" . $sitedetail['site_logo'];

									// if (file_exists($oldfile)) {
									// 	unlink($oldfile);
									// }
								}
							} else {
								$this->theme->set_message('section image size is too large', 'error');
							}
						} else {
							$this->theme->set_message('section image extension is not .jpg or .png formate', 'error');
						}
					}
                }
                $menu_id = $this->certification_menu_model->save_menu($data_array);
                $this->certification_menu_model->save_question($menu_id, $data['question']);
                
                if ($id == 0) {
                    $this->theme->set_message(lang('certification_menu-add-success'), 'success');
                } else {
                    $this->theme->set_message(lang('certification_menu-edit-success'), 'success');
                }
                if(isset($data_array['id']))
                {
                    $data_action = 'Update';
                }
                else
                {
                    $data_action = 'Create';
                }

                // $site_id = $this->session->userdata[$this->section_name]['site_id'];
                // $user_id = $this->session->userdata[$this->section_name]['user_id'];
                // saveAuditTrail($user_id, $site_id, 'certification_menus ('.$certification_menu_name.')', $data_action);

                redirect(BASE_ADMIN_URL_CUSTOM . 'certification_menu');
                exit;
            }
        } else {
            $id = 0;
            $title = "";
            $status = 0;
            $slug = '';
            $section_description = '';
        }
        // Pass data to view file
        $data['id'] = $id;
        $data['title'] = $title;
        $data['status'] = $status;
        $data['section_description'] = $section_description;
        $data['certification_menus'] = $certification_menus;
        $data['main_menu_id'] = $main_menu_id;
        //Logic
        if ($id == 0) {
            $data['id'] = 0;
            $status = 1;
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('add-certification_menu'));
            $this->breadcrumb->add(lang('add-certification_menu'));
        } else {
            $data['id'] = $id;
            $status = $data['status'];
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('edit-certification_menu'));
            $this->breadcrumb->add(lang('edit-certification_menu'));
        }
        //Render view
        $this->theme->view($data, 'admin_add');
    }
}
