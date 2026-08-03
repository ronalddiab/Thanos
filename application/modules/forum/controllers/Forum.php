<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  FORUM Controller
 *
 *
 * @package CIDemoApplication
 * @subpackage Forum
 * @author AVSH
 */
class Forum extends Base_Front_Controller {
    /*
     * Create a Instance
     */

    public $sectorList;
    public $industriesList;

    function __construct() {
        parent::__construct();

        // Login check for admin
        $this->access_control($this->access_rules());

        // load helpers
        $this->load->helper(array('url', 'cookie'));
        $this->load->helper('download');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->library('session');

        // load required models
        $this->load->model('forum_category_model');
        $this->load->model('forum_post_model');
        $this->load->model('forum_topics_model');
        $this->load->model('forum_activity_log_model');
        $this->load->model('urls/urls_model');
        
        // Breadcrumb settings
		$this->breadcrumb->add(lang('home'), base_url() . BASE_ADMIN_URL_CUSTOM);
        $this->breadcrumb->add(lang('forum'), base_url() . 'forum/forum_listing');

        //set theme
        $this->theme->set_theme("front");
    }

    /*
     * access_rules() - this function is used for access control
     * based on login
     * * - all can access ['users' => array('*')]
     * @ - logged person can access ['users' => array('@')]
     */

    /**
     * Function access_rules to check login
     */
    public function access_rules() {
        return array(
            array(
                'actions' => array('index', 'forum_listing', 'action', 'forum_post','posts', 'myforum', 'mycontribution', 'today_thread', 'popular_post','discussion_rules'),
                'users' => array('@')
            ),
        );
    }

    /**
     * Function index for fetching Forum Categories,we can choose category and see their forums
     */
    public function index() {

        //Initialize
        $data = array();
        $categories = array();

        // language code
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];

        //Load data for url listing
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_category_model->record_per_page = $this->record_per_page;
        $this->forum_category_model->offset = $offset;
        $language_list = $this->languages_model->get_languages();
        $language_code = strip_tags($language_code);

        // Logic
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];


        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['search_term'])) {
                $this->forum_category_model->search_term = $data['search_term'];
            }
            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->forum_category_model->sort_by = $data['sort_by'];
                $this->forum_category_model->sort_order = $data['sort_order'];
            }
        }
        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }
        $this->forum_category_model->front = 0;
        $categories = $this->forum_category_model->get_category_listing($language_id);
        foreach ($categories as $k => $v) {
            $categories[$k]['categories']['total_forum' . $categories[$k]['categories']['category_id']] = $this->forum_post_model->total_forum_by_category($categories[$k]['categories']['category_id'], $language_id);
        }

        //Variable assignments to view
        $data['categories'] = $categories;
        $data['page_number'] = $this->page_number;
        $this->forum_category_model->_record_count = true;
        $total_records = $this->forum_category_model->get_category_listing($language_id);
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->forum_category_model->search_term;
        $data['sort_by'] = $this->forum_category_model->sort_by;
        $data['sort_order'] = $this->forum_category_model->sort_order;
        $data['language_id'] = $language_id;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;

        //Create page-title
        $this->theme->set('page_title', lang('forum_category'));
        //Render view
        $this->theme->view($data);
    }

    /**
     * Function forum_listing for display Forum of selected category.
     */
    //public function forum_listing($slug_url) {
    public function forum_listing() {

        $this->session->set_custom_userdata($this->section_name, "postback_url", 'forum_listing');

        $getForumRules = getForumRules();
        $industry_list = array();//$this->sector_model->get_all_industry();
        $industry = "";

        $sector = "";
        $sector_list_result = array();//$this->sector_model->get_record_listing();
        $sector_list[''] = '---All Sectors---';
        foreach ($sector_list_result as $result) {
            $sector_list[$result['R']['sector_id']] = $result['R']['sector_name'];
        }

        $data = array();
        $forums = array();
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_code = strip_tags($language_code);
        $language_list = $this->languages_model->get_languages();
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if ($this->input->post()) {
            $data = $this->input->post();

            // echo "<pre>"; print_r($data); exit;

            if (isset($data['keywords'])) {
                $this->forum_post_model->keywords = $data['keywords'];
                $this->session->set_custom_userdata($this->section_name, "forum_keywords", $this->input->post('keywords'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_keywords", "");
            }

            if (isset($data['date_from']) || isset($data['date_to'])) {
                $this->session->set_custom_userdata($this->section_name, "forum_date_from", $this->input->post('date_from'));
                $this->session->set_custom_userdata($this->section_name, "forum_date_to", $this->input->post('date_to'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_date_from", "");
                $this->session->set_custom_userdata($this->section_name, "forum_date_to", "");
            }

            if (isset($data['industry_id'])) {
                // $this->forum_post_model->keywords = $data['keywords'];
                $this->session->set_custom_userdata($this->section_name, "forum_industry_id", $this->input->post('industry_id'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_industry_id", "");
            }

            if (isset($data['sector_id'])) {
                // $this->forum_post_model->keywords = $data['keywords'];
                $this->session->set_custom_userdata($this->section_name, "forum_sector_id", $this->input->post('sector_id'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_sector_id", "");
            }

            if (isset($data['member'])) {
                // $this->forum_post_model->keywords = $data['keywords'];
                $this->session->set_custom_userdata($this->section_name, "forum_member", $this->input->post('member'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_member", "");
            }

            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->forum_post_model->sort_by = $data['sort_by'];
                $this->forum_post_model->sort_order = $data['sort_order'];
            }
        }

        $this->forum_post_model->is_private = 0;
        $this->forum_post_model->front = 0;

        $forums = $this->forum_post_model->get_forum_listing_front($language_id);

        //Variable assignments to view
        $this->forum_post_model->_record_count = true;
        $total_records = $this->forum_post_model->get_forum_listing_front($language_id);
        $data['forums'] = $forums;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;
        $data['keywords'] = $this->forum_post_model->keywords;
        $data['language_id'] = $language_id;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['industry_list'] = $industry_list;
        $data['industry'] = $industry;
        $data['sector_list'] = $sector_list;
        $data['sector'] = $sector;
        $data['getForumRules'] = $getForumRules;
        $data['postback_url'] = $this->session->userdata[$this->section_name]['postback_url'];

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        foreach ($forums as $k => $v) {
            $this->forum_topics_model->id = $v['forum_post']['id'];
            $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count($language_id);
        }
        //Create page-title
        $this->theme->set('page_title', lang('forum'));
        //Render view
        $this->theme->view($data, 'forum_listing');
    }

    /**
     * Function action for add & edit Forum.
     */
    function action($action = "add", $id = 0) {
        $getForumRules = getForumRules();

        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
            $this->theme->set_message(lang('pls-login-access'), 'info');
            redirect(site_url() . 'users/login');
        }

        //Type Casting
        $action = trim(strip_tags($action));
        $id = trim(strip_tags($id));

        //Initialize
        $data = array();
        $data_array = array();
        $slug_url = "";

        $industry_list = array();//$this->sector_model->get_all_industry();
        $industry = "";

        $sector_list1[''] = '---Select Sector---';
        $sector1 = "";

        $sector = "";
        $sector_list_result = array();//$this->sector_model->get_record_listing();
        $sector_list[''] = '---All Sectors---';
        foreach ($sector_list_result as $result) {
            $sector_list[$result['R']['sector_id']] = $result['R']['sector_name'];
        }

        $tag_list = array();//$this->tag_model->get_all_tag();
        $tags = array();
        $IsDraft = "";

        // language code

        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_list = $this->languages_model->get_languages();
        $language_code = strip_tags($language_code);
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        // Logic
        $this->forum_category_model->front = 0;
        $categories = $this->forum_category_model->get_category_listing($language_id);
        if ($action == "edit") {
            $this->forum_post_model->id = $id;
            $this->forum_post_model->front = 0;
            $forum_data = $this->forum_post_model->get_forum_listing("", $language_id);
            $data['id'] = $id;
            $data['action'] = $action;
        }
        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();
            //Type Casting
            $id = intval($data['id']);

            //Variable Assignment

            $forum_title = trim(strip_tags($data['forum_title']));
            $slug_url = trim(strip_tags($data['forum_title']));

            $forum_description = html_entity_decode(trim($data['forum_description']));
            $forum_category = trim(strip_tags($data['forum_category']));
            $is_private = trim(strip_tags($data['is_private']));
            $tag_id = isset($data['tag_id']) ? $data['tag_id'] : "";

            $industry_id = trim(strip_tags($data['industry_id']));
            $sector_id = trim(strip_tags($data['sector_id']));

            $IsDraft = $data['is_draft'];
            // field name, error message, validation rules
            $this->form_validation->set_rules('forum_title', lang('forum_title'), 'trim|required|callback_check_unique_slug');

            if ($this->form_validation->run($this) == TRUE) {

                $data_array['forum_post_title'] = $forum_title;
                $data_array['slug_url'] = $slug_url;
                $data_array['forum_post_text'] = $forum_description;
                $data_array['category_id'] = $forum_category;
                $data_array['is_private'] = $is_private;
                $data_array['lang_id'] = $language_id;
                $data_array['industry_id'] = $industry_id;
                $data_array['sector_id'] = $sector_id;
                $data_array['is_draft'] = $IsDraft;

                if ($action == "add") {
                    $data_array['status'] = 1;
                } else {
                    $data_array['status'] = $data['status'];
                    $data_array['modified_by'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
                }
                $data_array['created_by'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
                $data_array['id'] = $id;

                $last_id = $this->forum_post_model->forum_adder($data_array);

                $this->forum_post_model->save_tags($tag_id, $last_id);
                $this->forum_post_model->update_slug($last_id);

                if ($id == 0) {
                    $this->theme->set_message(lang('forum-add-success'), 'success');
                } else {
                    $this->theme->set_message(lang('forum-edit-success'), 'success');
                }

                redirect('forum/forum_listing');
                exit;
            } else {
                $this->theme->set_message(lang('slug_exist'), 'error');
            }
        } else {
            $id = 0;
            $forum_title = "";
            $forum_description = "";
            $forum_category = "";
        }

        //Variable assignments to view
        if ($action == "edit") {
            $ForumTags = $this->forum_post_model->getForumTags($forum_data[0]['forum_post']['id']);
            $data['forum_name'] = $forum_data[0]['forum_post']['forum_post_title'];
            $data['status'] = $forum_data[0]['forum_post']['status'];
            $data['forum_description'] = $forum_data[0]['forum_post']['forum_post_text'];
            $data['is_private'] = $forum_data[0]['forum_post']['is_private'];
            $data['forum_category'] = $forum_data[0]['forum_post']['category_id'];
            $industry = $forum_data[0]['forum_post']['industry_id'];
            $sector = $forum_data[0]['forum_post']['sector_id'];
            $tags = $ForumTags;
        }
        $data['action'] = $action;
        $data['categories'] = $categories;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['industry_list'] = $industry_list;

        $data['sector_list'] = $sector_list;
        $data['sector'] = $sector;

        $data['sector_list1'] = $sector_list1;
        $data['sector1'] = $sector1;

        $data['tag_list'] = $tag_list;
        $data['industry'] = $industry;
        $data['tags'] = $tags;
        $data['getForumRules'] = $getForumRules;


        //create breadcrumbs & page-title
        if ($id == 0) {
            $status = 1;
            $this->theme->set('page_title', lang('add-forum'));
            $this->breadcrumb->add(lang('add-forum'));
        } else {
            $status = $data['status'];
            $this->theme->set('page_title', lang('edit-forum'));
            $this->breadcrumb->add(lang('edit-forum'));
        }

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        $this->theme->view($data, 'action');
    }

    /**
     * Function check_unique_slug for check is slug unique?
     */
    public function check_unique_slug() {
        $slug_url = $this->input->post('forum_title');

        //Type Casting
        $slug_url = trim(strip_tags($slug_url));

        //Logic
        $result = $this->forum_post_model->check_unique_slug($slug_url);

        if (count($result) > 0) {
            $this->form_validation->set_message('check_unique_slug', lang('msg_available_slug_url'));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function forum_post for display Forum Posts.
     */
    public function posts($slug_url = '') {
        $getForumRules = getForumRules();

        //Type Casting
        $slug_url = trim(strip_tags($slug_url));

        //Initialize
        $data = array();
        $data_array = array();
        $config = array();
        $topic_title = "";
        $topic_text = "";
        //Load data for url listing
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        // $offset = get_offset($this->page_number, $this->record_per_page);
        // $this->forum_topics_model->record_per_page = $this->record_per_page;
        // $this->forum_topics_model->offset = $offset;
        //Type Casting
        $language_code = strip_tags($language_code);
        $language_list = $this->languages_model->get_languages();

        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }

        // Logic
        $id_temp = $this->forum_post_model->get_id_from_slug($slug_url);
        $id = $id_temp['forum_post']['id'];
        $uri = mysql_real_escape_string($_SERVER['REQUEST_URI']);
        $view_count = $this->forum_activity_log_model->get_view_count($uri);
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();

            $topic_title = trim(strip_tags($data['topic_title']));
            $topic_text = html_entity_decode(trim($data['topic_text']));
            $id = $data['id'];

            //$this->form_validation->set_rules('topic_title', lang('topic_title'), 'trim|required');
            $this->form_validation->set_rules('topic_text', lang('topic_text'), 'trim|required');
            $hid_slug = $data['slug_url'];

            if ($this->form_validation->run()) {
                //Variable assignments
                $data_array['post_id'] = $id;
                $data_array['id'] = 0;
                $data_id['id'] = $id;
                $data_array['topic_title'] = $topic_title;
                $data_array['topic_text'] = $topic_text;
                $data_array['lang_id'] = $language_id;
                $data_array['created_by'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
                $forum_title = trim(strip_tags($data['topic_title']));

                // field name, error message, validation rules
                $this->form_validation->set_rules('topic_title', lang('topic_title'), 'trim|required');
                $this->form_validation->set_rules('topic_text', lang('topic_text'), 'trim|required');
//                if ($_FILES) {
//                    $config['upload_path'] = 'assets/uploads/forum_files';
//                    $config['allowed_types'] = '*';
//                    //$config['max_size'] = '10000';
//                    $config['max_width'] = '1024';
//                    $config['max_height'] = '768';
//                    $config['name'] = $_FILES['attachment']['name'];
//                    $this->load->library('upload', $config);
//
//                    $field_name = "attachment";
//
//                    if ($_FILES['attachment']['name'] && !$this->upload->do_upload('attachment')) {
//                        $error = array('error' => $this->upload->display_errors());
//                        echo $this->upload->display_errors();
//                    } else {
//                        $uploaded_data = array('upload_data' => $this->upload->data());
//                        $data_array['attachment'] = $config['name'];
//                    }
//                    if (isset($data_array['topic_title']) && $data_array['topic_title'] != '') {
//                        $this->forum_post_model->updated_date($data_id);
//                        $this->forum_topics_model->add_reply($data_array);
//                        $this->theme->set_message('Reply successfully added', 'success');
//                    } else {
//                        $this->theme->set_message('Please fill all fields', 'error');
//                    }
//                }

                $this->forum_post_model->updated_date($data_id);
                $this->forum_topics_model->add_reply($data_array);

                $this->theme->set_message(lang('reply-success-add'), 'success');
                redirect('forum/posts/' . $hid_slug);
                // redirect(current_url());
            } else {
                $this->theme->set_message('Please fill all fields', 'error');
                // redirect(current_url());
                // redirect('forum/forum_post/'.$hid_slug);
            }
        }

        $forum_first_post = $this->forum_post_model->forum_post_by_id($id, $language_id);
        $forum_tags = $this->forum_post_model->forum_tags($id);


        $cat_name = $this->forum_category_model->category_name_getter($forum_first_post['fp']['category_id'], $language_id);
        $category_name = $cat_name['categories']['title'];
        $this->forum_topics_model->front = 0;
        $forum_post_comments = $this->forum_topics_model->get_topics_from_post($id, $language_id);

        if(isset($this->session->userdata[$this->section_name]['postback_url'])){
            $this->forum_post_model->postback_url = $this->session->userdata[$this->section_name]['postback_url'];
        }


        $last_post = $this->forum_topics_model->last_update_datetime($id);

        //Variable assignments to view
        $data['forum_first_post'] = $forum_first_post;
        $data['forum_post_comments'] = $forum_post_comments;
        $data['id'] = $id;
        $data['slug_url'] = $slug_url;
        $data['last_post'] = $last_post;
        $data['view_count'] = $view_count;
        $data['page_number'] = $this->page_number;
        $this->forum_topics_model->id = $id;
        $this->forum_topics_model->_record_count = true;
        $total_records = $this->forum_topics_model->get_topics_from_post($id, $language_id);
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->forum_topics_model->search_term;
        $data['language_id'] = $language_id;
        $data['category_name'] = $category_name;
        $data['sort_by'] = $this->forum_topics_model->sort_by;
        $data['sort_order'] = $this->forum_topics_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['forum_tags'] = $forum_tags;
        $data['topic_title'] = $topic_title;
        $data['topic_text'] = $topic_text;
        $data['postback_url'] = $this->forum_post_model->postback_url;

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        //create breadcrumbs & page-title
        $this->theme->set('page_title', lang('forum-topics'));
        $this->breadcrumb->add(lang('forum-topics'));
        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;
        $data['getForumRules'] = $getForumRules;
        //Render view
        $this->theme->view($data, 'forum_post');
    }

    function myforum() {
        $this->session->set_custom_userdata($this->section_name, "postback_url", 'myforum');

        $getForumRules = getForumRules();

        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
            $this->theme->set_message(lang('pls-login-access'), 'info');
            redirect(site_url() . 'users/login');
        }

        //Load data for url listing
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_code = strip_tags($language_code);
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;

        // language code
        $language_list = $this->languages_model->get_languages();
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
            $this->theme->set_message(lang('pls-login-access'), 'info');
            redirect(site_url() . 'users/login');
        }
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;
        $forums = $this->forum_post_model->get_forum_listing_for_member($this->session->userdata[$this->section_name]['user_id']);

        $data = array();

        $data['forums'] = $forums;
        $data['page_number'] = $this->page_number;
        $this->forum_post_model->_record_count = true;

        // $total_records = $this->forum_post_model->get_forum_listing_front($language_id);
        $total_records = $this->forum_post_model->get_forum_listing_for_member($this->session->userdata[$this->section_name]['user_id']);

        $data['total_records'] = $total_records;
        $data['language_id'] = $language_id;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;
        $data['postback_url'] = $this->session->userdata[$this->section_name]['postback_url'];

        foreach ($forums as $k => $v) {
            $this->forum_topics_model->id = $v['forum_post']['id'];
            $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count($language_id);
        }
        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        $data['getForumRules'] = $getForumRules;

        $this->breadcrumb->add(lang('my-thread'));
        $this->theme->view($data);
    }

    function mycontribution() {

        $this->session->set_custom_userdata($this->section_name, "postback_url", 'mycontribution');

        $getForumRules = getForumRules();

        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
            $this->theme->set_message(lang('pls-login-access'), 'info');
            redirect(site_url() . 'users/login');
        }

        //Load data for url listing
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_code = strip_tags($language_code);
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;

        // language code
        $language_list = $this->languages_model->get_languages();
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
            $this->theme->set_message(lang('pls-login-access'), 'info');
            redirect(site_url() . 'users/login');
        }

        $forums = $this->forum_post_model->get_conttribution_for_member($this->session->userdata[$this->section_name]['user_id']);

        $data = array();

        $data['forums'] = $forums;
        $data['page_number'] = $this->page_number;
        $this->forum_post_model->_record_count = true;

        // $total_records = $this->forum_post_model->get_forum_listing_front($language_id);
        $total_records = $this->forum_post_model->get_conttribution_for_member($this->session->userdata[$this->section_name]['user_id']);

        $data['total_records'] = $total_records;
        $data['language_id'] = $language_id;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;
        $data['postback_url'] = $this->session->userdata[$this->section_name]['postback_url'];

        foreach ($forums as $k => $v) {
            $this->forum_topics_model->id = $v['forum_post']['id'];
            $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count($language_id);
        }
        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        $data['getForumRules'] = $getForumRules;
        $this->breadcrumb->add(lang('my-contribution'));
        $this->theme->view($data);
    }

    function today_thread() {
        $this->session->set_custom_userdata($this->section_name, "postback_url", 'today_thread');

        $getForumRules = getForumRules();

        //Load data for url listing
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_code = strip_tags($language_code);
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;

        // language code
        $language_list = $this->languages_model->get_languages();
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        $data = array();

        $this->forum_post_model->is_private = 0;
        $this->forum_post_model->front = 0;

        // $forums = $this->forum_post_model->get_forum_listing_front($language_id);
        $forums = $this->forum_post_model->get_today_thread($language_id);

        $data['forums'] = $forums;
        $data['page_number'] = $this->page_number;

        $this->forum_post_model->_record_count = true;

        // $total_records = $this->forum_post_model->get_forum_listing_front($language_id);
        $total_records = $this->forum_post_model->get_today_thread($language_id);
        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;

        $data['total_records'] = $total_records;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['language_id'] = $language_id;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['getForumRules'] = $getForumRules;
        $data['postback_url'] = $this->session->userdata[$this->section_name]['postback_url'];

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        foreach ($forums as $k => $v) {
            $this->forum_topics_model->id = $v['forum_post']['id'];
            $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count($language_id);
        }

        //Create page-title
        $this->theme->set('page_title', lang('forum'));
        $this->breadcrumb->add(lang('today-thread'));
        $this->theme->view($data);
    }

    function popular_post() {
        $this->session->set_custom_userdata($this->section_name, "postback_url", 'popular_post');

        $getForumRules = getForumRules();

        //Load data for url listing
        $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        $language_code = strip_tags($language_code);
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;

        // language code
        $language_list = $this->languages_model->get_languages();
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

//        if (!isset($this->session->userdata[$this->section_name]['user_id'])) {
//            $this->theme->set_message(lang('pls-login-access'), 'info');
//            redirect(site_url() . 'users/login');
//        }
        $forums = $this->forum_post_model->get_forum_listing_front($language_id);

        $data = array();

        $data['forums'] = $forums;
        $data['page_number'] = $this->page_number;

        $this->forum_post_model->_record_count = true;
        $total_records = $this->forum_post_model->get_forum_listing_front($language_id);

        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;

        $data['total_records'] = $total_records;
        $data['language_id'] = $language_id;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['postback_url'] = $this->session->userdata[$this->section_name]['postback_url'];

        if (!empty($forums)) {
            foreach ($forums as $k => $v) {
                $this->forum_topics_model->id = $v['forum_post']['id'];
                $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count($language_id);
                $data["forums"]['rly_count'][$k] = $this->forum_topics_model->record_count($language_id);
            }

            arsort($data["forums"]['rly_count']);
            $temp_array = array_keys($data["forums"]['rly_count']);

            unset($data["forums"]['rly_count']);

            $count = 0;
            foreach ($temp_array as $k => $v) {
                $data1["forums"][] = $data["forums"][$v];
            }

            $data['forums1'] = $data1["forums"];
        }

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        $data['getForumRules'] = $getForumRules;
        $this->breadcrumb->add(lang('popular-post'));
        $this->theme->view($data);
    }

    function forum_left_search_data() {
        $this->industriesList = array();//$this->sector_model->get_all_industry();
        $sector_list_result = array();//$this->sector_model->get_record_listing();
        $this->sectorList[''] = '---All Sectors---';
        foreach ($sector_list_result as $result) {
            $this->sectorList[$result['R']['sector_id']] = $result['R']['sector_name'];
        }
    }

    function discussion_rules(){
        $getForumRules = getForumRules();
        //create breadcrumbs & page-title
        $this->theme->set('page_title', lang('forum-rules'));
        $this->breadcrumb->add(lang('forum-rules'));
        $this->forum_left_search_data();
        $data['industry_list'] = $this->industriesList;
        $data['sector_list'] = $this->sectorList;
        $data['keywords'] = $this->forum_post_model->keywords;
        $data['getForumRules'] = $getForumRules;

        if (isset($this->session->userdata[$this->theme->get('section_name')]['user_id'])) {
            $data['logged_in'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];
        }

        //Render view
        $this->theme->view($data, 'discussion_rules');
    }

}
