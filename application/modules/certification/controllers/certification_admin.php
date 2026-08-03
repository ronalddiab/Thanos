<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');
class Certification_admin extends Base_Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->language = $this->uri->segment(4);
        $this->load->library('unit_test');
        $this->load->model('certification/Certification_model');   
        $this->load->model('certification_menu/Certification_menu_model'); 
    }

    /**
     * Function access_rules to check login
     */
    public function access_rules() {

        return array(
            array(
                'actions' => array('index','section','save','upload'),
                'users' => array('@'),
            )
        );
    }
    function section()
    {
        $currentUrl = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $currentUrl);
        $lastSegment = end($parts);
        $questions = array();
        $data =  $this->certification_model->get_section_info_by_slug($lastSegment);
        if(!empty($lastSegment) && count($data)>0)
        {
            $id = $data['id'];
            $parent_array = $this->certification_menu_model->getAncestors($id,1);
            $breadcrumb = '';

            foreach (array_reverse($parent_array) as $sec) {
                $breadcrumb .= '<a href="' .site_base_url(). $sec['link'] . '">' . $sec['title'] . '</a> > ';
            }
            
            $breadcrumb .= '<a href="'.site_base_url().$data['link'].'">'.$data['title'].'</a>';
            $section_image = $data['section_image'];

            $this->breadcrumb->add($breadcrumb);

            $user_id = $this->session->userdata[$this->section_name]['user_id'];
            $questions = $this->certification_menu_model->get_section_questions($id); 
            $data['questions'] = $questions;
            $answers = [];
            foreach($questions as $key=>$que)
            {
                $ans_arr = $this->certification_model->get_question_answer($que['id'],$user_id);
                $data['questions'][$key]['section_question_comments']= $ans_arr['section_question_comments'];
                $data['questions'][$key]['answer_id']= $ans_arr['id'];
                $data['questions'][$key]['section_question_answer']= $ans_arr['section_question_answer'];
            }
            $this->theme->view($data, 'admin_add');
        }
        else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . '/');
            exit;
        }
    }
    function save()
    {
        $currentUrl = $_SERVER['HTTP_REFERER'];
        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();
            $data_array= array();
            $data_array['user_id'] = $this->session->userdata[$this->section_name]['user_id'];
            $data_array['ans'] = $data['ans'];
            $ans = $this->certification_model->save_ans($data_array);
            redirect($currentUrl);
            exit;
        }
    }
    function upload()
    {
        $currentUrl = $_SERVER['HTTP_REFERER'];
        $data = $this->input->post();
        $section_id = $data['section_id'];
        if (isset($_FILES['section_image']['name'])) {

            $config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
            $config['max_size']       = '2048';
            $config['maintain_ratio'] = true;
            $config['width']          = 140;
            $config['height']         = 100;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            $valid_formats = array("jpg", "png");
            $imagename     = $_FILES['section_image']['name'];
            $size = $_FILES['section_image']['size'];
            $i    = strrpos($imagename, ".");

            if (!$i) {
                $ext = '';
            }

            $l              = strlen($imagename) - $i;
            $ext            = substr($imagename, $i + 1, $l);
            $section_image_name = 'section_image_' . rand(11111, 9999999) . '.' . $ext;

            if ($ext) {

                if (in_array($ext, $valid_formats)) {
                    // procedure further if and only if image size can not be more than 10MB.

                    if ($size < (1024 * 1024 * 10)) {

                        $uploadedfile = $_FILES['section_image']['tmp_name'];

                        $target_file  = BASE_PATH_CUSTOM . "/assets/uploads/" . $section_image_name;

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

                            $section_image               = trim(strip_tags($section_image_name));
                            $data_array['section_image'] = $section_image;
                            $data_array['id']            = $section_id;
                            $menu_id = $this->certification_menu_model->save_menu($data_array);
                            redirect($currentUrl);
                            exit;
                        }
                    } else {
                        $this->theme->set_message('section image size is too large', 'error');
                    }
                } else {
                    $this->theme->set_message('section image extension is not .jpg or .png formate', 'error');
                }
            }
        }
        else{
            redirect($currentUrl);
            exit;
        }
    }
}
