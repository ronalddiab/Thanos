<?php

/**
 *  Hotels Admin Controller
 *
 *  To perform hotel management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class hotels_admin extends Base_Admin_Controller {

    var $search_term;

    function __construct() {


        parent::__construct();

        $this->load->library('form_validation');
        $this->breadcrumb->add(lang('account-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'hotels');
        // Login check for admin
        $this->access_control($this->access_rules());
        $this->language = $this->uri->segment(4);
        $this->load->library('unit_test');
        $this->load->model('hotels/hotels_model');
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {

        return array(
            array(
                'actions' => array('index','edit','save','view_data'),
                'users' => array('@'),
            )
       
        );
    }

    function index() {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Sort Order
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->hotels_model->sort_by = $data['sort_by'];
                $this->hotels_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "hotel_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "hotel_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "hotel_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "hotel_sort_order", "");
            }
            // Sort Order

            if (isset($data['type']) && $data['type'] == 'active') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = base64_decode($val);
                }
                // Newly added
                if ($this->hotels_model->active_records($tempArr)) {
                    echo $this->theme->message(lang('hotel-active-success'), 'success');
                    exit;
                }
            }
            /*
            if (isset($data['type']) && $data['type'] == 'inactive') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = base64_decode($val);
                }
                // Newly added
                if ($this->hotels_model->inactive_records($tempArr)) {
                    echo $this->theme->message(lang('hotel-inactive-success'), 'success');
                    exit;
                }
            } */
        }

        if (!empty($this->session->userdata[$this->section_name]['hotel_sort_by'])) {
            $this->hotels_model->sort_by = $this->session->userdata[$this->section_name]['hotel_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['hotel_sort_order'])) {
            $this->hotels_model->sort_order = $this->session->userdata[$this->section_name]['hotel_sort_order'];
        }


        //Load data for url listing
        $hotels = $this->hotels_model->get_hotel_listing();
        $this->hotels_model->_record_count = true;
        $total_records = $this->hotels_model->get_hotel_listing();
        
        // Pass data to view file
        $data['hotels'] = $hotels;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->hotels_model->search_term;
        $data['sort_by'] = $this->hotels_model->sort_by;
        $data['sort_order'] = $this->hotels_model->sort_order;

        //Create page-title
        $this->theme->set('page_title', lang('hotel-management'));

        //Render view
        $this->theme->view($data);
    }

    /**
     * Function hotels_validation_rules to validate input
     */
    function hotels_validation_rules() {
        $this->form_validation->set_rules('hotel_name', lang('hotel-name'), 'trim|required|min_length[2]');
        $this->form_validation->set_rules('hotel_address', lang('hotel-address'), 'trim|required|min_length[2]');
        $this->form_validation->set_rules('hotel_phone', lang('hotel-phone'), 'trim|required|numeric|max_length[15]');
        $id = intval($this->input->post('id'));
    }

    function edit($id=0) {
        $id = intval($id);
        $action = trim(strip_tags($action));
        $type = custom_filter_input('integer', $id);

        $hotel_name = "";
        $hotel_address = "";
        $hotel_phone = "";
        //$status = "";
        $status = 1;

        // Logic
        $result = $this->hotels_model->get_hotel_detail($id);
        $data['hotel_id'] = $result['id'];
        if (!empty($result)) {
            //Variable assignment for edit view
            $hotel_name = $result['hotel_name'];
            $hotel_address = $result['hotel_address'];
            $hotel_phone = $result['hotel_phone'];
            //$status = isset($result['status']) ? $result['status'] : 1;
            $status = 1;
        } else {
            //If hotel not exist then redirecting to listing page
            $this->theme->set_message(lang('hotel-not-exist'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM.'hotels');
        }

        // Pass data to view file
        $data['id'] = $id;
        $data['hotel_name'] = $hotel_name;
        $data['hotel_logo'] = $result['hotel_logo'];
        $data['hotel_address'] = $hotel_address;
        $data['hotel_phone'] = $hotel_phone;
        $data['status'] = $status;
       
        //create breadcrumbs & page-title
        if (!empty($id)) {
            $this->theme->set('page_title', lang('edit-hotel'));
            $this->breadcrumb->add(lang('edit-hotel'));
        } else {
            $this->theme->set('page_title', lang('add-hotel'));
            $this->breadcrumb->add(lang('add-hotel'));
        }

        //Render view
        $this->theme->view($data, 'admin_add');
    }

    /**
     * Function save to insert/update hotel data
     */
    function save() {

        //set form validation to check server side validation
        $this->load->library('form_validation');

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();

            //Type Casting
            $id = intval($data['id']);
            $hotel_name = trim(strip_tags($data['hotel_name']));
            $hotel_address = trim(strip_tags($data['hotel_address']));
            $hotel_phone = trim(strip_tags($data['hotel_phone']));
            if ($id == 0) {
                $status = $data['status'];
            } else {
                $status = $data['status'];
            }
            
            $status = 1;

            // field name, error message, validation rules
            $this->hotels_validation_rules();
            $result = $this->hotels_model->get_hotel_detail($id);
            if (!empty($_FILES["hotel_logo"]["tmp_name"])) {
                $this->form_validation->set_rules('hotel_logo', lang('hotel-logo'), 'callback_valid_upload');
            }

            if ($this->form_validation->run($this)) {

                // Upload hotel logo
                if (isset($_FILES['hotel_logo']['name'])) {

                    $config['upload_path'] = BASE_PATH_CUSTOM . "/assets/uploads/";
                    $config['max_size'] = '2048';
                    $config['maintain_ratio'] = TRUE;
                    $config['width'] = 140;
                    $config['height'] = 100;

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    $imagename = $_FILES['hotel_logo']['name'];

                    $size = $_FILES['hotel_logo']['size'];
                    $i = strrpos($imagename, ".");
                    if (!$i) {
                        $ext = '';
                    }
                    $l = strlen($imagename) - $i;
                    $ext = substr($imagename, $i + 1, $l);
                    $hotel_logo_name = 'hotel_logo_' . rand(11111, 9999999) . '.' . $ext;
                    
                    $uploadedfile = $_FILES['hotel_logo']['tmp_name'];
                    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $hotel_logo_name;
                    $_movestatus = move_uploaded_file($uploadedfile, $target_file);

                    if (!$_movestatus) {
                        $this->theme->set_message('Hotel image is not uploaded', 'error');
                    } else {
                        $this->load->library('image_lib');
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $target_file;
                        $this->image_lib->clear();
                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        $hotel_logo = trim(strip_tags($hotel_logo_name));
                        $data_array['hotel_logo'] = $hotel_logo;

                        // Delete Old file
                        $oldfile = BASE_PATH_CUSTOM . "/assets/uploads/" . $result['hotel_logo'];
                        if (file_exists($oldfile)) {
                            unlink($oldfile);
                        }
                    }
                }


                $data_array['id'] = $id;
                $data_array['hotel_name'] = $hotel_name;
                $data_array['hotel_address'] = $hotel_address;
                $data_array['hotel_phone'] = $hotel_phone;
                $data_array['status'] = $status;

                $this->hotels_model->save_hotel($data_array);

                if ($id == 0) {
                    $this->theme->set_message(lang('hotel-add-success'), 'success');
                    $data_action = 'Create';
                } else {
                    $this->theme->set_message(lang('hotel-edit-success'), 'success');
                    $data_action = 'Update';
                }

                $site_id = $this->session->userdata[$this->section_name]['site_id'];
                $user_id = $this->session->userdata[$this->section_name]['user_id'];
                saveAuditTrail($user_id, $site_id, 'Hotels', $data_action);

                redirect(BASE_ADMIN_URL_CUSTOM.'hotels');
                exit;
            }
        } else {
            $id = 0;
            $hotel_name = "";
            $hotel_address = "";
            $hotel_phone = "";
            $status = 1;
        }

        // Pass data to view file
        $data['id'] = $id;
        $data['hotel_name'] = $hotel_name;
        $data['hotel_address'] = $hotel_address;
        $data['hotel_phone'] = $hotel_phone;
        $data['status'] = $status;

        //Logic
        if ($id == 0) {
            $data['hotel_id'] = 0;
            $status = 1;
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('add-hotel'));
            $this->breadcrumb->add(lang('add-hotel'));
        } else {
            $data['hotel_id'] = $id;
            //$status = $data['status'];
            $status = 1;
            //create breadcrumbs & page-title
            $this->theme->set('page_title', lang('edit-hotel'));
            $this->breadcrumb->add(lang('edit-hotel'));
        }

        //Render view
        $this->theme->view($data, 'admin_add');
    }

    public function view_data($id = 0) {
        $result = $this->hotels_model->get_hotel_detail($id);
        $data = array();
        $data = $result;
        $this->breadcrumb->add(lang('view-hotel'));
        $this->theme->view($data);
    }

    function valid_upload() {
        if (!empty($_FILES["hotel_logo"]["tmp_name"])) {
            $check = getimagesize($_FILES["hotel_logo"]["tmp_name"]);
            if ($check !== false) {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["hotel_logo"]["name"]);
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $this->form_validation->set_message('valid_upload', "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                    return false;
                } else {
                    // Image size validation
                    if ($_FILES["hotel_logo"]["size"] > 10485760) {
                        $this->form_validation->set_message('valid_upload', 'Sorry, your file is too large. Maximum image size shold be < 10MB');
                        return false;
                    } else {
                        return true;
                    }
                }
            } else {
                $this->form_validation->set_message('valid_upload', "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                return false;
            }
        } else {
            $this->form_validation->set_message('valid_upload', "The site logo is required.");
            return false;
        }
    }
}
