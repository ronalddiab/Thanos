<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Projects_admin extends Base_Admin_Controller {



    private $site_id = '';

    private $role_id = '';

    private $user_id = '';



    function __construct() {

        parent::__construct();

        $this->access_control($this->access_rules());

        $this->load->model('projects_model');

        $this->load->library('form_validation');

        $this->language = $this->uri->segment(4);



        $this->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : '';

        $this->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : '';

        $this->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;



        $this->projects_model->site_id = $this->site_id;

        $this->projects_model->role_id = $this->role_id;

        $this->projects_model->user_id = $this->user_id;

    }



    private function access_rules() {

        return array(

            array(

                'actions' => array('index', 'edit', 'view', 'viewplan', 'categories', 'category_edit', 'category_view', 'comments', 'comment_edit', 'comment_view', 'actionlist', 'addactionplan', 'actionplans', 'add_project_todo_comment', 'delete_project_todo_comment', 'add_project_todo_file', 'set_actionplan_expiry', 'listing', 'viewactionplans'),

                'users' => array('@'),

            ),

            array(

                'actions' => array('ajax_action_projects', 'ajax_action_todos'),

                'users' => array('*')

            )

        );

    }



    function listing() {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');



        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);

        $this->projects_model->record_per_page = $this->record_per_page;

        $this->projects_model->offset = $offset;



        if ($this->input->post()) {

            $data = $this->input->post();



            if (empty($data['page_number'])) {

                $this->session->set_custom_userdata($this->section_name, "projects_offset", "");

                $this->session->set_custom_userdata($this->section_name, "projects_page_number", "");

            }



            if (isset($data['search_term'])) {

                $this->projects_model->projects_search_term = trim($data['search_term']);

                $this->session->set_custom_userdata($this->section_name, "projects_search_term", $this->input->post('search_term'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "projects_search_term", "");

            }



            if (isset($data['sort_by']) && $data['sort_order']) {

                $this->projects_model->projects_sort_by = $data['sort_by'];

                $this->projects_model->projects_sort_order = $data['sort_order'];

                $this->session->set_custom_userdata($this->section_name, "projects_sort_by", $this->input->post('sort_by'));

                $this->session->set_custom_userdata($this->section_name, "projects_sort_order", $this->input->post('sort_order'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "projects_sort_by", "");

                $this->session->set_custom_userdata($this->section_name, "projects_sort_order", "");

            }



            $site_filter_role_allow = array(1, 2);

            if (in_array($this->role_id, $site_filter_role_allow)) {

                $this->projects_model->filter_admin_site_id = true;

                if (isset($data['filter_site_id'])) {

                    $this->session->set_custom_userdata($this->section_name, "filter_site_id", $this->input->post('filter_site_id', 0));

                }

            }



            if (isset($data['filter_category_id'])) {

                $this->session->set_custom_userdata($this->section_name, "filter_category_id", $this->input->post('filter_category_id', 0));

            }



            if (isset($data['filter_region_id'])) {

                $this->session->set_custom_userdata($this->section_name, "filter_region_id", $this->input->post('filter_region_id', 0));

            }



            if (isset($data['filter_color_id'])) {

                $this->session->set_custom_userdata($this->section_name, "filter_color_id", $this->input->post('filter_color_id', 0));

            }



            if (isset($data['type']) && $data['type'] == 'delete') {

                if ($this->projects_model->delete_action_plan_records($data['ids'], $data['acids'])) {

                    echo $this->theme->message(lang('projects-delete-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active') {

                if ($this->projects_model->active_records($data['ids'])) {

                    echo $this->theme->message(lang('projects-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive') {

                if ($this->projects_model->inactive_records($data['ids'])) {

                    echo $this->theme->message(lang('projects-inactive-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->projects_model->active_all_records()) {

                    echo $this->theme->message(lang('projects-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive_all') {

                if ($this->projects_model->inactive_all_records()) {

                    echo $this->theme->message(lang('projects-inactive-success'), 'success');

                    exit;

                }

            }

        }



        if (!empty($this->session->userdata[$this->section_name]['projects_search_term'])) {

            $this->projects_model->projects_search_term = trim($this->session->userdata[$this->section_name]['projects_search_term']);

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_sort_by'])) {

            $this->projects_model->projects_sort_by = $this->session->userdata[$this->section_name]['projects_sort_by'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_sort_order'])) {

            $this->projects_model->projects_sort_order = $this->session->userdata[$this->section_name]['projects_sort_order'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_offset'])) {

            $this->projects_model->projects_offset = $this->session->userdata[$this->section_name]['projects_offset'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_page_number'])) {

            $this->projects_model->projects_page_number = $this->session->userdata[$this->section_name]['projects_page_number'];

        }



        if (!is_null($this->session->userdata[$this->section_name]['filter_site_id'])) {

            $this->projects_model->filter_admin_site_id = true;

            $this->projects_model->filter_site_id = $this->session->userdata[$this->section_name]['filter_site_id'];

        } else {

            $this->projects_model->filter_admin_site_id = false;

            $this->projects_model->filter_site_id = null;

        }



        /* $site_filter_role_allow = array(1,2);

          if(in_array($this->role_id, $site_filter_role_allow)) {

          $this->site_id = 0;

          $this->projects_model->filter_site_id = 0;

          $this->projects_model->filter_admin_site_id = 0;

          if (isset($data['filter_site_id'])) {

          $this->session->set_custom_userdata($this->section_name, "filter_site_id", $this->input->post('filter_site_id',0));

          }

          } */



        if (!is_null($this->session->userdata[$this->section_name]['filter_category_id'])) {

            $this->projects_model->filter_category_id = $this->session->userdata[$this->section_name]['filter_category_id'];

        } else {

            $this->projects_model->filter_category_id = '';

        }



        if (!is_null($this->session->userdata[$this->section_name]['filter_region_id'])) {

            $this->projects_model->filter_region_id = $this->session->userdata[$this->section_name]['filter_region_id'];

        } else {

            $this->projects_model->filter_region_id = '';

        }



        if (!is_null($this->session->userdata[$this->section_name]['filter_color_id'])) {

            $this->projects_model->filter_color_id = $this->session->userdata[$this->section_name]['filter_color_id'];

        } else {

            $this->projects_model->filter_color_id = '';

        }



        $this->breadcrumb->add(lang('projects'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        $data = array();



        $projects = $this->projects_model->getProjects($this->site_id, $this->role_id);

        $this->projects_model->_record_count = true;

        $total_records = $this->projects_model->getProjects($this->site_id, $this->role_id);

        $data['projects'] = $projects;

        $data['page_number'] = $this->page_number;

        $data['total_records'] = $total_records;

        $data['search_term'] = $this->projects_model->projects_search_term;

        $data['filter_site_id'] = (!is_null($this->projects_model->filter_site_id)) ? $this->projects_model->filter_site_id : '';

        $data['filter_category_id'] = (!is_null($this->projects_model->filter_category_id)) ? $this->projects_model->filter_category_id : '';

        $data['filter_region_id'] = (!is_null($this->projects_model->filter_region_id)) ? $this->projects_model->filter_region_id : '';

        $data['filter_color_id'] = (!is_null($this->projects_model->filter_color_id)) ? $this->projects_model->filter_color_id : '';

        $data['sort_by'] = $this->projects_model->projects_sort_by;

        $data['sort_order'] = $this->projects_model->projects_sort_order;



        $this->projects_model->_record_count = false;

        $data['sites'] = $this->projects_model->get_site_listing($this->site_id, $this->role_id);

        $data['role_id'] = $this->role_id;

        $data['categories'] = $this->projects_model->get_categories_helper();

        $data['regions'] = $this->projects_model->get_regions_helper();

        /* $data['categories'] = $this->projects_model->get_categories_helper();

          $data['hotels'] = $this->hotels_model->get_hotel_list_helper();

          $data['sites'] = $this->sites_model->get_site_list_helper(); */



        $this->theme->set('page_title', 'Projects');

        $this->theme->view($data);

    }



    public function check_project_edit_unique($str, $field) {

        list($table, $field, $field1, $value1) = explode('.', $field);



        if (!empty($value1)) {

            $this->db->where($field1 . ' !=', $value1);

        }

        $this->db->where('status !=', -1);



        $query = $this->db->limit(1)->get_where($table, array($field => $str));



        $this->form_validation->set_message('check_project_edit_unique', lang('meg-project-already-exists'));



        //echo $this->db->last_query();exit;

        return $query->num_rows() === 0;

    }



    public function check_category_edit_unique($str, $field) {

        list($table, $field, $field1, $value1) = explode('.', $field);



        if (!empty($value1)) {

            $this->db->where($field1 . ' !=', $value1);

        }

        $this->db->where('status !=', -1);



        $query = $this->db->limit(1)->get_where($table, array($field => $str));



        $this->form_validation->set_message('check_category_edit_unique', lang('meg-category-already-exists'));

        return $query->num_rows() === 0;

    }



    function edit($id = 0) {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        $this->breadcrumb->add('Sub Category & EMAs', base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        if ($this->input->post('submit') == '1') {

            $checkid = $this->input->post('id');

            $this->form_validation->set_rules('project_name', lang('project-name'), 'trim|required|callback_check_project_edit_unique[project_info.project_name.id.' . $checkid . ']');

            //$this->form_validation->set_rules('project_description', lang('project-description'), 'trim|required');

            //$this->form_validation->set_rules('start_date', lang('start-date'), 'trim|required');

            //$this->form_validation->set_rules('end_date', lang('end-date'), 'trim|required');



            $data['id'] = $this->input->post('id');

            $data['project_name'] = $this->input->post('project_name');

            $data['project_description'] = $this->input->post('project_description');

            $data['project_category_id'] = $this->input->post('project_category_id');



            $data['hotel_id'] = $this->session->userdata[$this->section_name]['hotel_id'];

            //$data['hotel_id']= $this->input->post('hotel_id');                        

            if ($this->role_id == 1 || $this->role_id == 2) {

                $data['site_id'] = 0;

            } else {

                $data['site_id'] = $this->site_id;

            }



            /* if ($this->role_id != 1) {

              $data['site_id']= $this->site_id;

              }else{

              $data['site_id']= $this->input->post('site_id');

              } */



            $data['start_date'] = $this->input->post('start_date');

            $data['end_date'] = $this->input->post('end_date');

            //$data['status'] = (int)$this->input->post('status');

            $data['status'] = 1; //set default status to 1

            if ($this->form_validation->run($this)) {

                $todos = $this->input->post('todo');

                // Upload Image from here

                $config['upload_path'] = BASE_PATH_CUSTOM . "/assets/uploads/";

                $config['max_size'] = '2048';

                $config['maintain_ratio'] = TRUE;

                $config['width'] = 140;

                $config['height'] = 100;



                $this->load->library('upload', $config);

                $this->upload->initialize($config);



                $valid_formats = array("jpg", "png");

                if (isset($_FILES['todo']['name']['todo_image']) && !empty($_FILES['todo']['name']['todo_image'])) {

                    foreach ($_FILES['todo']['name']['todo_image'] as $key => $value) {

                        $imagename = $_FILES['todo']['name']['todo_image'][$key];



                        $size = $_FILES['todo']['size']['todo_image'][$key];

                        $i = strrpos($imagename, ".");

                        if (!$i) {

                            $ext = '';

                        }

                        $l = strlen($imagename) - $i;

                        $ext = substr($imagename, $i + 1, $l);

                        $project_actionplan_image_name = 'project_actionplan_' . rand(11111, 9999999) . '.' . $ext;

                        if ($ext) {

                            if (in_array($ext, $valid_formats)) {

                                // procedure further if and only if image size can not be more than 10MB.

                                if ($size < (1024 * 1024 * 10)) {

                                    $uploadedfile = $_FILES['todo']['tmp_name']['todo_image'][$key];

                                    ;

                                    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_actionplan_image_name;

                                    $_movestatus = move_uploaded_file($uploadedfile, $target_file);



                                    if (!$_movestatus) {

                                        $this->theme->set_message('Todo image is not uploaded', 'error');

                                    } else {

                                        $todo_image = trim(strip_tags($project_actionplan_image_name));

                                        $todos['todo_image'][$key] = $todo_image;



                                        /* // Delete Old file

                                          $oldfile = BASE_PATH_CUSTOM."/assets/uploads/".$sitedetail['todo_image'];

                                          if(file_exists($oldfile)){

                                          unlink($oldfile);

                                          } */

                                    }

                                }else{

                                    $this->theme->set_message('site image size is too large', 'error');

                                    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/edit/'.$id);

                                    exit;

                                }

                            }else{

                                $this->theme->set_message("Please upload image file.", 'error');

                                redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'projects/edit/'.$id);

                                exit;

                            }

                        }

                    }

                }



                // Save project detail

                $data['user_id'] = $this->session->userdata[$this->section_name]['user_id'];

                $id = $this->projects_model->save_project($data);



                // Save project todos

                $this->projects_model->save_project_todo($id, $todos, $this->section_name, $data['site_id']);



                $this->theme->set_message(lang('project-save-success'), 'success');

                redirect(BASE_ADMIN_URL_CUSTOM . 'projects');

            }

        }



        $project = $this->projects_model->getProject($id);

        if (!empty($project)) {

            $data['id'] = $project['id'];

            $data['project_name'] = $project['project_name'];

            $data['project_description'] = $project['project_description'];

            $data['status'] = (int) $project['status'];

            $data['project_category_id'] = $project['project_category_id'];

            $data['hotel_id'] = $project['hotel_id'];

            $data['site_id'] = $project['site_id'];

            $data['start_date'] = $project['start_date'];

            $data['end_date'] = $project['end_date'];



            $project_todos = $this->projects_model->get_todos($id);

            $data['todos'] = $project_todos;



            if ($this->role_id != 1 && $this->site_id != $project['site_id']) {

                $this->theme->set_message(lang('unauthorize'), 'error');

                redirect(BASE_ADMIN_URL_CUSTOM . 'projects');

            }



            $this->breadcrumb->add('Edit Sub Category', base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        } else {

            $data['todos'] = array();

            $this->breadcrumb->add('Add Sub Category', base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        }



        $data['categories'] = $this->projects_model->getCategoriesList();

        $data['hotels'] = $this->hotels_model->get_hotel_listing();

        $data['role_id'] = $this->role_id;



        $site_id = isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

        $role_id = isset($this->session->userdata['admin']['role_id']) ? $this->session->userdata['admin']['role_id'] : '';

        $data['sites'] = $this->projects_model->get_site_listing($site_id, $role_id);



        $this->theme->set('page_title', lang('Edit EMA'));

        $this->theme->view($data);

    }



    function view($id = 0) {

        $site_id = isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

        $role_id = isset($this->session->userdata['admin']['role_id']) ? $this->session->userdata['admin']['role_id'] : '';

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        $project = $this->projects_model->getProject($id);



        if (empty($project)) {

            show_404();

        }



        $this->projects_model->_record_count = false;

        $categories_all = $this->projects_model->getCategories();

        $categories = array();

        foreach ($categories_all as $value) {

            $categories[$value['pc']['id']] = $value['pc']['name'];

        }

        $data['categories'] = $categories;

        /*

        $hotels_all = $this->hotels_model->get_hotel_listing();

        $hotels = array();

        foreach ($hotels_all as $value) {

            $hotels[$value['h']['id']] = $value['h']['hotel_name'];

        }

        $data['hotels'] = $hotels;

        

        $sites_all = $this->projects_model->get_site_listing($site_id, $role_id);

        $sites = array();

        foreach ($sites_all as $value) {

            $sites[$value['s']['id']] = $value['s']['site_location_name'];

        }

        $data['sites'] = $sites;

        */

        $data['id'] = $project['id'];

        $data['project_name'] = $project['project_name'];

        $data['project_description'] = $project['project_description'];

        $data['status'] = (int) $project['status'];

        $data['project_category_id'] = $project['project_category_id'];

        $data['hotel_id'] = $project['hotel_id'];

        $data['site_id'] = $project['site_id'];

        $data['start_date'] = $project['start_date'];

        $data['end_date'] = $project['end_date'];



        $project_todos = $this->projects_model->get_todos($id);

        $data['todos'] = $project_todos;

        $data['categories'] = $this->projects_model->getCategories();

        //$data['hotels'] = $this->hotels_model->get_hotel_listing();

        //$data['sites'] = $this->projects_model->get_site_listing($site_id, $role_id);



        if ($this->role_id != 1 && $this->site_id != $project['site_id']) {

            $this->theme->set_message(lang('unauthorize'), 'error');

            redirect(BASE_ADMIN_URL_CUSTOM . 'projects');

        }



        $this->breadcrumb->add('Sub Category & EMAs', base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        $this->breadcrumb->add(lang('view-project'), current_url());



        $this->theme->set('page_title', lang('project'));

        $this->theme->view($data);

    }



    function viewplan($id = 0) {

        $site_id = isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

        $role_id = isset($this->session->userdata['admin']['role_id']) ? $this->session->userdata['admin']['role_id'] : '';

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        $project = $this->projects_model->getProject($id);



        if (empty($project)) {

            show_404();

        }



        $this->projects_model->_record_count = false;

        $categories_all = $this->projects_model->getCategories();

        $categories = array();

        foreach ($categories_all as $value) {

            $categories[$value['pc']['id']] = $value['pc']['name'];

        }

        $data['categories'] = $categories;



        $hotels_all = $this->hotels_model->get_hotel_listing();

        $hotels = array();

        foreach ($hotels_all as $value) {

            $hotels[$value['h']['id']] = $value['h']['hotel_name'];

        }

        $data['hotels'] = $hotels;



        $sites_all = $this->projects_model->get_site_listing($site_id, $role_id);

        $sites = array();

        foreach ($sites_all as $value) {

            $sites[$value['s']['id']] = $value['s']['site_location_name'];

        }

        $data['sites'] = $sites;



        $data['id'] = $project['id'];

        $data['project_name'] = $project['project_name'];

        $data['project_description'] = $project['project_description'];

        $data['status'] = (int) $project['status'];

        $data['project_category_id'] = $project['project_category_id'];

        $data['hotel_id'] = $project['hotel_id'];

        $data['site_id'] = $project['site_id'];

        $data['start_date'] = $project['start_date'];

        $data['end_date'] = $project['end_date'];



        $project_todos = $this->projects_model->get_todos($id);

        $data['todos'] = $project_todos;

        $data['categories'] = $this->projects_model->getCategories();

        $data['hotels'] = $this->hotels_model->get_hotel_listing();

        $data['sites'] = $this->projects_model->get_site_listing($site_id, $role_id);



        if ($this->role_id != 1 && $this->site_id != $project['site_id']) {

            $this->theme->set_message(lang('unauthorize'), 'error');

            redirect(BASE_ADMIN_URL_CUSTOM . 'projects');

        }



        $this->breadcrumb->add(lang('projects'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        $this->breadcrumb->add(lang('view-project'), current_url());



        $this->theme->set('page_title', lang('project'));

        $this->theme->view($data);

    }



    function categories() {

        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);

        $this->projects_model->categories_record_per_page = $this->record_per_page;

        $this->projects_model->categories_offset = $offset;



        if ($this->input->post()) {

            $data = $this->input->post();



            if (empty($data['page_number'])) {

                $this->session->set_custom_userdata($this->section_name, "categories_offset", "");

                $this->session->set_custom_userdata($this->section_name, "categories_page_number", "");

            }



            if (isset($data['search_term'])) {

                $this->projects_model->categories_search_term = trim($data['search_term']);

                $this->session->set_custom_userdata($this->section_name, "categories_search_term", $this->input->post('search_term'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "categories_search_term", "");

            }



            if (isset($data['sort_by']) && $data['sort_order']) {

                $this->projects_model->categories_sort_by = $data['sort_by'];

                $this->projects_model->categories_sort_order = $data['sort_order'];

                $this->session->set_custom_userdata($this->section_name, "categories_sort_by", $this->input->post('sort_by'));

                $this->session->set_custom_userdata($this->section_name, "categories_sort_order", $this->input->post('sort_order'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "categories_sort_by", "");

                $this->session->set_custom_userdata($this->section_name, "categories_sort_order", "");

            }





            if (isset($data['type']) && $data['type'] == 'delete') {

                if ($this->projects_model->delete_records_category($data['ids'])) {

                    echo $this->theme->message(lang('projects-categories-delete-success'), 'success');

                    exit;

                } else {

                    echo $this->theme->message(lang('category-contains-project-error'), 'error');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active') {

                if ($this->projects_model->active_records_category($data['ids'])) {

                    echo $this->theme->message(lang('projects-categories-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive') {

                if ($this->projects_model->inactive_records_category($data['ids'])) {

                    echo $this->theme->message(lang('projects-categories-inactive-success'), 'success');

                    exit;

                } else {

                    echo $this->theme->message(lang('category-contains-project-error'), 'error');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->projects_model->active_all_records_category()) {

                    echo $this->theme->message(lang('projects-categories-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive_all') {

                if ($this->projects_model->inactive_all_records_category()) {

                    echo $this->theme->message(lang('projects-categories-inactive-success'), 'success');

                    exit;

                } else {

                    echo $this->theme->message(lang('category-contains-project-error'), 'error');

                    exit;

                }

            }

        }



        if (!empty($this->session->userdata[$this->section_name]['categories_search_term'])) {

            $this->projects_model->categories_search_term = trim($this->session->userdata[$this->section_name]['categories_search_term']);

        }

        if (!empty($this->session->userdata[$this->section_name]['categories_sort_by'])) {

            $this->projects_model->categories_sort_by = $this->session->userdata[$this->section_name]['categories_sort_by'];

        }

        if (!empty($this->session->userdata[$this->section_name]['categories_sort_order'])) {

            $this->projects_model->categories_sort_order = $this->session->userdata[$this->section_name]['categories_sort_order'];

        }

        if (!empty($this->session->userdata[$this->section_name]['categories_offset'])) {

            $this->projects_model->categories_offset = $this->session->userdata[$this->section_name]['categories_offset'];

        }

        if (!empty($this->session->userdata[$this->section_name]['categories_page_number'])) {

            $this->projects_model->categories_page_number = $this->session->userdata[$this->section_name]['categories_page_number'];

        }



        $this->breadcrumb->add(lang('project-categories'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories');

        $data = array();



        $categories = $this->projects_model->getCategories();

        $this->projects_model->_record_count = true;

        $total_records = $this->projects_model->getCategories();

        $data['categories'] = $categories;

        $data['page_number'] = $this->page_number;

        $data['total_records'] = $total_records;

        $data['search_term'] = $this->projects_model->categories_search_term;

        $data['sort_by'] = $this->projects_model->categories_sort_by;

        $data['sort_order'] = $this->projects_model->categories_sort_order;



        $this->theme->set('page_title', 'Categories');

        $this->theme->view($data);

    }



    function category_edit($id = 0) {

        $this->breadcrumb->add(lang('project-categories'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories');

        if ($this->input->post('submit') == '1') {

            $checkid = $this->input->post('id');

            $this->form_validation->set_rules('name', lang('topic_title'), 'trim|required|callback_check_category_edit_unique[project_categories.name.id.' . $checkid . ']');

            //$this->form_validation->set_rules('description', lang('topic_text'), 'trim|required');



            $data['id'] = $this->input->post('id');

            $data['name'] = $this->input->post('name');

            $data['description'] = $this->input->post('description');

            $data['status'] = (int) $this->input->post('status');

            if ($this->form_validation->run($this)) {

                $data['user_id'] = $this->session->userdata[$this->section_name]['user_id'];

                $id = $this->projects_model->save_category($data);

                $this->theme->set_message(lang('category-save-success'), 'success');

                redirect(BASE_ADMIN_URL_CUSTOM . 'projects/categories');

            }

        }



        $category = $this->projects_model->getCategory($id);

        if (!empty($category)) {

            $data['id'] = $category['id'];

            $data['name'] = $category['name'];

            $data['description'] = $category['description'];

            $data['status'] = (int) $category['status'];

            $this->breadcrumb->add(lang('edit-category'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories');

        } else {

            $this->breadcrumb->add(lang('add-category'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories');

        }

        $this->theme->set('page_title', lang('project-category'));

        $this->theme->view($data);

    }



    function category_view($id = 0) {

        $category = $this->projects_model->getCategory($id);

        if (empty($category)) {

            show_404();

        }



        $data['id'] = $category['id'];

        $data['name'] = $category['name'];

        $data['description'] = $category['description'];

        $data['status'] = (int) $category['status'];



        $this->breadcrumb->add(lang('project-categories'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/categories');

        $this->breadcrumb->add(lang('category-view'), current_url());



        $this->theme->set('page_title', lang('project-category'));

        $this->theme->view($data);

    }



    /* Project Comments */



    function comments() {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        $this->load->model('users/users_model');



        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);

        $this->projects_model->record_per_page = $this->record_per_page;

        $this->projects_model->offset = $offset;



        if ($this->input->post()) {

            $data = $this->input->post();



            if (empty($data['page_number'])) {

                $this->session->set_custom_userdata($this->section_name, "offset", "");

                $this->session->set_custom_userdata($this->section_name, "page_number", "");

            }



            if (isset($data['search_term'])) {

                $this->projects_model->search_term = trim($data['search_term']);

                $this->session->set_custom_userdata($this->section_name, "search_term", $this->input->post('search_term'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "search_term", "");

            }



            if (isset($data['sort_by']) && $data['sort_order']) {

                $this->projects_model->sort_by = $data['sort_by'];

                $this->projects_model->sort_order = $data['sort_order'];

                $this->session->set_custom_userdata($this->section_name, "sort_by", $this->input->post('sort_by'));

                $this->session->set_custom_userdata($this->section_name, "sort_order", $this->input->post('sort_order'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "sort_by", "");

                $this->session->set_custom_userdata($this->section_name, "sort_order", "");

            }





            if (isset($data['type']) && $data['type'] == 'delete') {

                if ($this->projects_model->delete_records_comment($data['ids'])) {

                    echo $this->theme->message(lang('comments-delete-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active') {

                if ($this->projects_model->active_records_comment($data['ids'])) {

                    echo $this->theme->message(lang('comments-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive') {

                if ($this->projects_model->inactive_records_comment($data['ids'])) {

                    echo $this->theme->message(lang('comments-inactive-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->projects_model->active_all_records_comment()) {

                    echo $this->theme->message(lang('comments-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive_all') {

                if ($this->projects_model->inactive_all_records_comment()) {

                    echo $this->theme->message(lang('comments-inactive-success'), 'success');

                    exit;

                }

            }

        }



        if (!empty($this->session->userdata[$this->section_name]['search_term'])) {

            $this->projects_model->search_term = trim($this->session->userdata[$this->section_name]['search_term']);

        }

        if (!empty($this->session->userdata[$this->section_name]['sort_by'])) {

            $this->projects_model->sort_by = $this->session->userdata[$this->section_name]['sort_by'];

        }

        if (!empty($this->session->userdata[$this->section_name]['sort_order'])) {

            $this->projects_model->sort_order = $this->session->userdata[$this->section_name]['sort_order'];

        }

        if (!empty($this->session->userdata[$this->section_name]['offset'])) {

            $this->projects_model->offset = $this->session->userdata[$this->section_name]['offset'];

        }

        if (!empty($this->session->userdata[$this->section_name]['page_number'])) {

            $this->projects_model->page_number = $this->session->userdata[$this->section_name]['page_number'];

        }



        $this->breadcrumb->add(lang('projects'), base_url() . BASE_ADMIN_URL_CUSTOM . 'projects');

        $data = array();



        $comments = $this->projects_model->getComments();

        $this->projects_model->_record_count = true;

        $total_records = $this->projects_model->getComments();

        $data['comments'] = $comments;

        $data['page_number'] = $this->page_number;

        $data['total_records'] = $total_records;

        $data['search_term'] = $this->projects_model->search_term;

        $data['sort_by'] = $this->projects_model->sort_by;

        $data['sort_order'] = $this->projects_model->sort_order;



        $this->projects_model->_record_count = false;





        /* $data['categories'] = $this->projects_model->get_categories_helper();

          $data['projects'] = $this->projects_model->get_projects_helper();

          $data['hotels'] = $this->hotels_model->get_hotel_list_helper();

          $data['sites'] = $this->sites_model->get_site_list_helper();

          $data['users'] = $this->users_model->get_user_list_helper(); */



        $this->theme->set('page_title', 'Projects');

        $this->theme->view($data);

    }



    function comment_edit($id = 0) {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        $this->breadcrumb->add(lang('project'), base_url() . BASE_ADMIN_URL_CUSTOM . '/projects/comments');

        if ($this->input->post('submit') == '1') {

            $this->form_validation->set_rules('comments', lang('comment'), 'trim|required');



            $data['id'] = $this->input->post('id');

            $data['comments'] = $this->input->post('comments');

            $data['project_id'] = $this->input->post('project_id');

            $data['hotel_id'] = $this->input->post('hotel_id');

            //$data['site_id']= $this->input->post('site_id');

            if ($this->role_id != 1) {

                $data['site_id'] = $this->site_id;

            } else {

                $data['site_id'] = $this->input->post('site_id');

            }



            $data['status'] = (int) $this->input->post('status');

            if ($this->form_validation->run($this)) {

                $data['user_id'] = $this->session->userdata[$this->section_name]['user_id'];

                $id = $this->projects_model->save_comment($data);

                $this->theme->set_message(lang('comment-save-success'), 'success');

                redirect(BASE_ADMIN_URL_CUSTOM . 'projects/comments');

            }

        }



        $project_comment = $this->projects_model->getComment($id);

        if (!empty($project_comment)) {

            $data['id'] = $project_comment['id'];

            $data['comments'] = $project_comment['comments'];

            $data['status'] = (int) $project_comment['status'];

            $data['project_id'] = $project_comment['project_id'];

            $data['hotel_id'] = $project_comment['hotel_id'];

            $data['site_id'] = $project_comment['site_id'];

        }



        $data['categories'] = $this->projects_model->get_categories_helper();

        $data['projects'] = $this->projects_model->get_projects_helper();

        $data['hotels'] = $this->hotels_model->get_hotel_list_helper();



        $site_id = isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

        $role_id = isset($this->session->userdata['admin']['role_id']) ? $this->session->userdata['admin']['role_id'] : '';

        $data['sites'] = $this->projects_model->get_site_listing($site_id, $role_id);



        $this->theme->set('page_title', lang('project'));

        $this->theme->view($data);

    }



    function comment_view($id = 0) {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');

        //$project = $this->projects_model->getProject($id);

        $project_comment = $this->projects_model->getComment($id);



        if (empty($project_comment)) {

            show_404();

        }



        $this->projects_model->_record_count = false;

        $categories_all = $this->projects_model->getCategories();

        $categories = array();

        foreach ($categories_all as $value) {

            $categories[$value['pc']['id']] = $value['pc']['name'];

        }

        $data['categories'] = $categories;



        $hotels_all = $this->hotels_model->get_hotel_listing();

        $hotels = array();

        foreach ($hotels_all as $value) {

            $hotels[$value['h']['id']] = $value['h']['hotel_name'];

        }

        $data['hotels'] = $hotels;



        /* $sites_all = $this->projects_model->get_site_listing($site_id,$role_id);

          $sites = array();

          foreach ($sites_all as $value) {

          $sites[$value['s']['id']] = $value['s']['site_location_name'];

          }

          $data['sites'] = $sites; */



        $data['id'] = $project['id'];

        /* $data['comments']       =$project['comments'];

          $data['status']     =(int)$project['status'];

          $data['project_id']     =$project['project_id'];

          $data['hotel_id']     = $project['hotel_id'];

          $data['site_id']     = $project['site_id']; */

        $data['id'] = $project_comment['id'];

        $data['comments'] = $project_comment['comments'];

        $data['status'] = (int) $project_comment['status'];

        $data['project_id'] = $project_comment['project_id'];

        $data['hotel_id'] = $project_comment['hotel_id'];

        $data['site_id'] = $project_comment['site_id'];



        $data['categories'] = $this->projects_model->get_categories_helper();

        $data['projects'] = $this->projects_model->get_projects_helper();

        $data['hotels'] = $this->hotels_model->get_hotel_list_helper();

        $site_id = isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

        $role_id = isset($this->session->userdata['admin']['role_id']) ? $this->session->userdata['admin']['role_id'] : '';

        $data['sites'] = $this->projects_model->get_site_listing($site_id, $role_id);



        $this->breadcrumb->add(lang('projects'), base_url() . BASE_ADMIN_URL_CUSTOM . '/projects/comments');

        $this->breadcrumb->add(lang('comment-view'), current_url());



        $this->theme->set('page_title', lang('comment'));

        $this->theme->view($data);

    }



    function actionlist($project_id = 0) {

        // Set site_id = 0 for fetching general projects

        //$this->projects_model->site_id=0;

        $project = $this->projects_model->getProjectForActionplan($project_id);

        $project_todos = $this->projects_model->get_todos($project_id);



        // Reset actual site_id for fetching sites actionplans

        //$this->projects_model->site_id=$this->site_id;

        $action_plans = $this->projects_model->getActionCountForProject($project_id);



        foreach ($project_todos as $key => $todo) {

            $project_todos[$key]['actionplan'] = $action_plans[$todo['id']];

        }



        //pre($project_todos);

        $data['project_todos'] = $project_todos;

        $data['project'] = $project;

        $data['role_id'] = $this->role_id;

        $this->theme->set('page_title', lang('Actions'));

        if (empty($project)) {

            show_404();

        }

        $this->breadcrumb->add(lang('actionlist'));

        $this->theme->view($data);

    }



    function actionplans() {

        if ($this->input->post()) {

            $data = $this->input->post();

            $site_id = (int) isset($this->session->userdata['admin']['site_id']) ? $this->session->userdata['admin']['site_id'] : '';

            if (isset($data['type']) && $data['type'] == 'delete' && $site_id > 0) {

                if ($this->projects_model->delete_action_plan_records($site_id, $data['ids'])) {

                    echo $this->theme->message('Action Plans has been deleted successfully', 'success');

                    exit;

                }

            }

        }



        //$this->projects_model->site_id = 0;

        $projects_categories = $this->projects_model->getCategories();

        $site_id = $this->site_id;

        $actiondata = array();

        $actiondata['site_id'] = $this->site_id;

        $actiondata['user_id'] = $this->user_id;

        $tdata['user_id'] = $this->user_id;

        $tdata['site_id'] = $this->site_id;



        $is_actionplans = false;

        if (!empty($projects_categories)) {

            foreach ($projects_categories as $key => $category) {

                $todocount = 0;

                $projects = $this->projects_model->getPublicProjects($category['pc']['id']);

                foreach ($projects as $key1 => $project) {

                    $actiondata['project_id'] = $project['p']['id'];



                    $project_todos = $this->projects_model->get_actionplans_todos($actiondata);

                    foreach ($project_todos as $tkey => $todo) {

                        $tdata['todo_id'] = $todo['id'];

                        $project_todo_comments = $this->projects_model->getTodoComments($tdata);

                        $project_todos[$tkey]['project_todo_comments'] = $project_todo_comments;



                        $project_todo_files = $this->projects_model->getTodoFiles($tdata);

                        $project_todos[$tkey]['project_todo_files'] = $project_todo_files;

                        $is_actionplans = true;

                    }

                    $projects[$key1]['p']['project_todos'] = $project_todos;

                    $todocount+=count($project_todos);

                    /* $project_comments = $this->projects_model->getTodoComments($project['p']['id']);

                      $projects[$key1]['p']['project_comments'] = $project_comments; */

                }

                $projects_categories[$key]['pc']['category_static_image'] = str_replace(' ', '_', $projects_categories[$key]['pc']['name']) . '.png';

                $projects_categories[$key]['pc']['projects_todo_count'] = $todocount;

                $projects_categories[$key]['pc']['projects'] = $projects;

            }

        }



        //pre($projects_categories);

        $data['is_actionplans'] = $is_actionplans;

        $data['projects_categories'] = $projects_categories;



        $data['action_categories'] = $this->projects_model->getCategoriesList();

		

        $this->breadcrumb->add(lang('actionplans'));

        $this->theme->set('page_title', lang('Actions'));

        $this->theme->view($data);

    }

    

    function viewactionplans($site_id) {

        if ($this->input->post()) {

            $data = $this->input->post();

            $site_id = (int) $site_id;            

        }



        //$this->projects_model->site_id = 0;

        $projects_categories = $this->projects_model->getCategories();

        $actiondata = array();

        $actiondata['site_id'] = $site_id;

        $actiondata['user_id'] = $this->user_id;

        $tdata['user_id'] = $this->user_id;

        $tdata['site_id'] = $site_id;



        $is_actionplans = false;

        if (!empty($projects_categories)) {

            foreach ($projects_categories as $key => $category) {

                $todocount = 0;

                $projects = $this->projects_model->getPublicProjects($category['pc']['id']);

                foreach ($projects as $key1 => $project) {

                    $actiondata['project_id'] = $project['p']['id'];



                    $project_todos = $this->projects_model->get_actionplans_todos_bysite($actiondata);
                    
                    foreach ($project_todos as $tkey => $todo) {

                        // pre($todo);

                        $tdata['todo_id'] = $todo['id'];

                        $project_todo_comments = $this->projects_model->getTodoComments($tdata);

                        $project_todos[$tkey]['project_todo_comments'] = $project_todo_comments;



                        $project_todo_files = $this->projects_model->getTodoFiles($tdata);

                        $project_todos[$tkey]['project_todo_files'] = $project_todo_files;

                        $is_actionplans = true;

                    }

                    $projects[$key1]['p']['project_todos'] = $project_todos;

                    $todocount+=count($project_todos);

                    /* $project_comments = $this->projects_model->getTodoComments($project['p']['id']);

                      $projects[$key1]['p']['project_comments'] = $project_comments; */

                }

                $projects_categories[$key]['pc']['category_static_image'] = str_replace(' ', '_', $projects_categories[$key]['pc']['name']) . '.png';

                $projects_categories[$key]['pc']['projects_todo_count'] = $todocount;

                $projects_categories[$key]['pc']['projects'] = $projects;

            }

        }



        //pre($projects_categories);

        $this->projects_model->_record_count = false;

        $data['sites'] = $this->projects_model->get_site_listing($this->site_id, $this->role_id);

        $data['site_id'] = $site_id;

        $data['is_actionplans'] = $is_actionplans;

        $data['projects_categories'] = $projects_categories;



        $data['action_categories'] = $this->projects_model->getCategoriesList();



        $this->breadcrumb->add(lang('actionplans'));

        $this->theme->set('page_title', lang('Actions'));

        // pre($data);

        $this->theme->view($data);

    }



    function ajax_action_projects() {

        $category_id = $this->input->post('category_id', 0);

        $projects = $this->projects_model->getPublicProjects($category_id);

        echo json_encode($projects);

        exit;

    }



    function ajax_action_todos() {

        $project_id = $this->input->post('project_id', 0);

        $project_todos = $this->projects_model->get_todos($project_id);

        echo json_encode($project_todos);

        exit;

    }



    function addactionplan() {

        $postdata = array();

        $return = array();

        $data = $this->input->post();	

        // pre($date_add());

        $postdata['todo_id'] = $data['todo_id'];

        $postdata['site_id'] = $this->site_id;

        $postdata['user_id'] = $this->user_id;

        if(!empty($data['siteId'])){

            $postdata['site_id'] = $data['siteId'];

        }

        $postdata['target_date'] = (isset($data['target_date']) && !empty($data['target_date'])) ? date('Y-m-d H:i:s', strtotime($data['target_date'])) : '0000-00-00 00:00:00';

        $postdata['completed_date'] = (isset($data['completed_date']) && !empty($data['completed_date'])) ? date('Y-m-d H:i:s', strtotime($data['completed_date'])) : '0000-00-00 00:00:00';

        $postdata['status'] = (isset($data['status']) && !empty($data['status'])) ? $data['status'] : 0;

        $postdata['kwh_savings'] = (isset($data['kwh_savings']) && !empty($data['kwh_savings'])) ? trim($data['kwh_savings']) : '';

        $postdata['cost_savings'] = (isset($data['cost_savings']) && !empty($data['cost_savings'])) ? trim($data['cost_savings']) : '';



        if ($this->user_id != 0 && !empty($postdata['todo_id'])) {

            // pre($postdata);

            $is_added = $this->projects_model->saveActionPlan($postdata);

            // pre($is_added);

            //$this->theme->set_message(lang('action-plan-add-success'), 'success');



            if ($is_added == 1) {

                echo $this->theme->message(lang('action-plan-update-success'), 'success');

            } else {

                echo $this->theme->message(lang('action-plan-add-success'), 'success');

            }

        } else {

            $this->theme->set_message(lang('action-plan-add-fail'), 'error');

            echo $this->theme->message(lang('action-plan-add-fail'), 'error');

        }

        exit;

    }



    function add_project_todo_comment() {



        $data['comment_id'] = $this->input->post('comment_id');

        $data['comments'] = $this->input->post('commentbox');

        $data['todo_id'] = $this->input->post('todo_id');

        $data['hotel_id'] = 1; // Needs to fetch from session



        $post_site_id = $this->input->post('site_id');



        $data['site_id'] = (isset($post_site_id) && !empty($post_site_id))?$post_site_id:$this->site_id;

        $data['status'] = 1;

        $data['user_id'] = $this->user_id; //$this->session->userdata[$this->section_name]['user_id'];



        if (empty($data['comments'])) {

            echo $this->theme->message(lang('comment-save-fail'), 'error');

            exit;

        }





        $result = $this->projects_model->save_todo_comment($data);



        if ($result) {

            echo $this->theme->message(lang('comment-save-success'), 'success');

        } else {

            echo $this->theme->message(lang('comment-save-fail'), 'error');

        }

        exit;

    }



    function delete_project_todo_comment() {

        if ($this->input->post('is_file') == 0) {

            $data['comment_id'] = $this->input->post('comment_id');

            $data['todo_id'] = $this->input->post('todo_id');

            $data['site_id'] = $this->site_id;



            $result = $this->projects_model->delete_todo_comment($data);

            if ($result) {

                echo $this->theme->message(lang('comments-delete-success'), 'success');

            } else {

                echo $this->theme->message(lang('comments-delete-fail'), 'error');

            }

        } else {

            $data['comment_id'] = $this->input->post('comment_id');

            $data['todo_id'] = $this->input->post('todo_id');

            $data['site_id'] = $this->site_id;

            $data['file'] = trim($this->input->post('file_name'));

            $file_name = trim($this->input->post('file_name'));



            $result = $this->projects_model->delete_todo_comment_file($data);

            $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $file_name;

            if (file_exists($target_file) && $result) {

                unlink($target_file);

                echo $this->theme->message(lang('file-delete-success'), 'success');

            } else {

                echo $this->theme->message(lang('file-delete-fail'), 'error');

            }

        }

        exit;

    }



    function add_project_todo_file() {

        $data['todo_id'] = $this->input->post('todo_id');



        // Upload File

        $target_dir = "uploads/";

        $target_file = $target_dir . basename($_FILES["file"]["name"]);

        $ext = pathinfo($target_file, PATHINFO_EXTENSION);



        $project_todo_file_name = 'project_todo_' . $data['todo_id'] . '_' . rand(11111, 9999999) . '.' . $ext;

        $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $project_todo_file_name;

        $_movestatus = move_uploaded_file($_FILES['file']['tmp_name'], $target_file);



        if ($_movestatus) {

            $data['hotel_id'] = 1; // Needs to fetch from session

            

            $post_site_id = $this->input->post('site_id');

            $data['site_id'] = (isset($post_site_id) && !empty($post_site_id))?$post_site_id:$this->site_id;

            //$data['site_id'] = $this->site_id;

            $data['status'] = 1;

            $data['user_id'] = $this->user_id; //$this->session->userdata[$this->section_name]['user_id'];

            $data['file'] = $project_todo_file_name;



            $result = $this->projects_model->save_todo_file($data);

            if ($result) {

                echo $this->theme->set_message(lang('file-upload-success'), 'success');

            } else {

                echo $this->theme->set_message(lang('file-upload-fail'), 'error');

            }

        } else {

            echo $this->theme->set_message(lang('file-upload-fail'), 'error');

        }



        $redirect_url = $this->input->post('redirect_url');



        if(!empty($redirect_url)){

            redirect(BASE_ADMIN_URL_CUSTOM . $redirect_url);

        }else{

            redirect(BASE_ADMIN_URL_CUSTOM . 'projects/actionplans');

        }

    }



    function set_actionplan_expiry() {

        $this->projects_model->setActionplanExpiry();

        echo 'complete';

        exit;

    }



    function index() {

        $this->load->model('hotels/hotels_model');

        $this->load->model('sites/sites_model');



        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);

        $this->projects_model->record_per_page = $this->record_per_page;

        $this->projects_model->offset = $offset;



        if ($this->input->post()) {

            $data = $this->input->post();



            if (empty($data['page_number'])) {

                $this->session->set_custom_userdata($this->section_name, "projects_offset", "");

                $this->session->set_custom_userdata($this->section_name, "projects_page_number", "");

            }



            if (isset($data['search_term'])) {

                $this->projects_model->projects_search_term = trim($data['search_term']);

                $this->session->set_custom_userdata($this->section_name, "projects_search_term", $this->input->post('search_term'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "projects_search_term", "");

            }



            if (isset($data['sort_by']) && $data['sort_order']) {

                $this->projects_model->projects_sort_by = $data['sort_by'];

                $this->projects_model->projects_sort_order = $data['sort_order'];

                $this->session->set_custom_userdata($this->section_name, "projects_sort_by", $this->input->post('sort_by'));

                $this->session->set_custom_userdata($this->section_name, "projects_sort_order", $this->input->post('sort_order'));

            } else {

                $this->session->set_custom_userdata($this->section_name, "projects_sort_by", "");

                $this->session->set_custom_userdata($this->section_name, "projects_sort_order", "");

            }



            $site_filter_role_allow = array(1, 2);

            if (in_array($this->role_id, $site_filter_role_allow)) {

                $this->projects_model->filter_admin_site_id = true;

                if (isset($data['filter_site_id'])) {

                    $this->session->set_custom_userdata($this->section_name, "filter_site_id", $this->input->post('filter_site_id', 0));

                }

            }



            if (isset($data['filter_category_id'])) {

                $this->session->set_custom_userdata($this->section_name, "filter_category_id", $this->input->post('filter_category_id', 0));

            }



            if (isset($data['filter_region_id'])) {

                $this->session->set_custom_userdata($this->section_name, "filter_region_id", $this->input->post('filter_region_id', 0));

            }



            if (isset($data['type']) && $data['type'] == 'check_delete') {

                echo $this->projects_model->get_delete_todo_all_records($data['ids'], $data['pid']);

                exit;

            }

            if (isset($data['type']) && $data['type'] == 'delete') {

                if ($this->projects_model->delete_todo_records($data['ids'], $data['catdata'])) {

                    echo $this->theme->message(lang('projects-delete-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active') {

                if ($this->projects_model->active_records($data['ids'])) {

                    echo $this->theme->message(lang('projects-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive') {

                if ($this->projects_model->inactive_records($data['ids'])) {

                    echo $this->theme->message(lang('projects-inactive-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->projects_model->active_all_records()) {

                    echo $this->theme->message(lang('projects-active-success'), 'success');

                    exit;

                }

            }

            if (isset($data['type']) && $data['type'] == 'inactive_all') {

                if ($this->projects_model->inactive_all_records()) {

                    echo $this->theme->message(lang('projects-inactive-success'), 'success');

                    exit;

                }

            }

        }



        if (!empty($this->session->userdata[$this->section_name]['projects_search_term'])) {

            $this->projects_model->projects_search_term = trim($this->session->userdata[$this->section_name]['projects_search_term']);

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_sort_by'])) {

            $this->projects_model->projects_sort_by = $this->session->userdata[$this->section_name]['projects_sort_by'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_sort_order'])) {

            $this->projects_model->projects_sort_order = $this->session->userdata[$this->section_name]['projects_sort_order'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_offset'])) {

            $this->projects_model->projects_offset = $this->session->userdata[$this->section_name]['projects_offset'];

        }

        if (!empty($this->session->userdata[$this->section_name]['projects_page_number'])) {

            $this->projects_model->projects_page_number = $this->session->userdata[$this->section_name]['projects_page_number'];

        }



        if (!is_null($this->session->userdata[$this->section_name]['filter_site_id'])) {

            $this->projects_model->filter_admin_site_id = true;

            $this->projects_model->filter_site_id = $this->session->userdata[$this->section_name]['filter_site_id'];

        } else {

            $this->projects_model->filter_admin_site_id = false;

            $this->projects_model->filter_site_id = null;

        }



        /* $site_filter_role_allow = array(1,2);

          if(in_array($this->role_id, $site_filter_role_allow)) {

          $this->site_id = 0;

          $this->projects_model->filter_site_id = 0;

          $this->projects_model->filter_admin_site_id = 0;

          if (isset($data['filter_site_id'])) {

          $this->session->set_custom_userdata($this->section_name, "filter_site_id", $this->input->post('filter_site_id',0));

          }

          } */



        if (!is_null($this->session->userdata[$this->section_name]['filter_category_id'])) {

            $this->projects_model->filter_category_id = $this->session->userdata[$this->section_name]['filter_category_id'];

        } else {

            $this->projects_model->filter_category_id = '';

        }



        if (!is_null($this->session->userdata[$this->section_name]['filter_region_id'])) {

            $this->projects_model->filter_region_id = $this->session->userdata[$this->section_name]['filter_region_id'];

        } else {

            $this->projects_model->filter_region_id = '';

        }



        $this->breadcrumb->add('Sub Category & EMA', base_url() . BASE_ADMIN_URL_CUSTOM . 'projects/listing');

        $data = array();



        $projects = $this->projects_model->getProjectListings($this->site_id, $this->role_id);

        $this->projects_model->_record_count = true;

        $total_records = $this->projects_model->getProjectListings($this->site_id, $this->role_id);

        $data['projects'] = $projects;

        $data['page_number'] = $this->page_number;

        $data['total_records'] = $total_records;

        $data['search_term'] = $this->projects_model->projects_search_term;

        $data['filter_site_id'] = (!is_null($this->projects_model->filter_site_id)) ? $this->projects_model->filter_site_id : '';

        $data['filter_category_id'] = (!is_null($this->projects_model->filter_category_id)) ? $this->projects_model->filter_category_id : '';

        $data['filter_region_id'] = (!is_null($this->projects_model->filter_region_id)) ? $this->projects_model->filter_region_id : '';

        $data['sort_by'] = $this->projects_model->projects_sort_by;

        $data['sort_order'] = $this->projects_model->projects_sort_order;



        $this->projects_model->_record_count = false;

        $data['sites'] = $this->projects_model->get_site_listing($this->site_id, $this->role_id);

        $data['role_id'] = $this->role_id;

        $data['categories'] = $this->projects_model->get_categories_helper();

        $data['regions'] = $this->projects_model->get_regions_helper();

        /* $data['categories'] = $this->projects_model->get_categories_helper();

          $data['hotels'] = $this->hotels_model->get_hotel_list_helper();

          $data['sites'] = $this->sites_model->get_site_list_helper(); */



        $this->theme->set('page_title', 'Sub Category & EMA');

        $this->theme->view($data);

    }



}

