<?php

/**
 *  FORUM Admin Controller
 *
 *  To perform FORUM management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 * @author AVSH
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Forum_admin extends Base_Admin_Controller {

    function __construct() {
        parent::__construct();
        // Login check for admin
        $this->access_control($this->access_rules());

        // Load required helpers
        $this->load->helper('download');
        $this->load->library('form_validation');
        $this->load->library('session');

        // load required models
        $this->load->model('forum_category_model');
        $this->load->model('forum_post_model');
        $this->load->model('forum_topics_model');
        $this->load->model('forum_activity_log_model');
        $this->load->model('urls/urls_model');
        $this->load->model('users/users_model');
        //$this->load->model('tag/tag_model');


        // Breadcrumb settings
        //$this->breadcrumb->add(lang('forum'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
    }

    /**
     * Function access_rules to check login
     */
    private function access_rules() {
        return array(
            array(
                //'actions' => array('index', 'ajax_forum_listing', 'delete_forum', 'delete_topic', 'forum_listing', 'action', 'forum_post', 'topic_edit', 'ajax_index', 'check_unique_slug', 'view_data', 'moderator_list', 'action_moderator', 'delete', 'set_permission', 'save_permission', 'archive'),
                'actions' => array('index', 'ajax_forum_listing', 'delete_forum', 'delete_topic', 'forum_listing', 'action', 'forum_post', 'topic_edit', 'ajax_index', 'check_unique_slug', 'view_data', 'delete', 'archive'),
                'users' => array('@'),
            )
        );
    }

    /**
     * Function index for fetching Forum Categories,we can choose category and see their forums
     */
    /*public function index($language_code = '') {
        //Type Casting
        $language_code = strip_tags($language_code);

        //Initialize
        $data = array();
        $categories = array();

        //Load data for url listing
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_category_model->record_per_page = $this->record_per_page;
        $this->forum_category_model->offset = $offset;

        // language code
        $language_list = $this->languages_model->get_languages();
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        // Pass data to view file

        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages_list'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;

        //Create page-title
        $this->theme->set('page_title', lang('forum_category'));
        //Render view
        $this->theme->view($data);
    }*/

    /**
     * Function ajax_index for fetching index page view.
     */
    function index() {
        //Initialize
        $data = array();
        $categories = array();
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_category_model->record_per_page = $this->record_per_page;
        $this->forum_category_model->offset = $offset;

        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "forum_category_offset", "");
                $this->session->set_custom_userdata($this->section_name, "forum_category_page_number", "");
            }

            if (isset($data['search_term'])) {
                $this->forum_category_model->search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "forum_category_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_category_search_term", "");
            }

            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->forum_category_model->sort_by = $data['sort_by'];
                $this->forum_category_model->sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "forum_category_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "forum_category_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "forum_category_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "forum_category_sort_order", "");
            }
        }


        if (!empty($this->session->userdata[$this->section_name]['forum_category_search_term'])) {
            $this->forum_category_model->search_term = trim($this->session->userdata[$this->section_name]['forum_category_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['forum_category_sort_by'])) {
            $this->forum_category_model->sort_by = $this->session->userdata[$this->section_name]['forum_category_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['email_template_sort_order'])) {
            $this->forum_category_model->sort_order = $this->session->userdata[$this->section_name]['forum_category_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['forum_category_offset'])) {
            $this->forum_category_model->offset = $this->session->userdata[$this->section_name]['forum_category_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['forum_category_page_number'])) {
            $this->page_number = $this->session->userdata[$this->section_name]['forum_category_page_number'];
        }

        //Logic
        $categories = $this->forum_category_model->get_category_listing();
        foreach ($categories as $k => $v) {
            $categories[$k]['categories']['total_forum' . $categories[$k]['categories']['category_id']] = $this->forum_post_model->total_forum_by_category($categories[$k]['categories']['category_id']);
        }

        //Variable assignments to view
        $data['categories'] = $categories;
        $data['page_number'] = $this->page_number;
        $this->forum_category_model->_record_count = true;
        $total_records = $this->forum_category_model->get_category_listing();
        $data['total_records'] = $total_records; // $this->forum_category_model->record_count($language_id);
        $data['search_term'] = $this->forum_category_model->search_term;
        $data['sort_by'] = $this->forum_category_model->sort_by;
        $data['sort_order'] = $this->forum_category_model->sort_order;

        $this->breadcrumb->add(lang('forum_category'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
        //Render view
        $this->theme->view($data);
    }

    /**
     * Function action for add & edit Forum.
     */
    function action($action = "add", $id = 0, $language_code = '') {

        //pre($this->session->userdata[$this->section_name]);exit;
        if ($action == 'add') {
            if (isset($this->session->userdata[$this->section_name]['permissions']) && !empty($this->session->userdata[$this->section_name]['permissions'])) {
                if (!in_array('admin.forum.action.add', $this->session->userdata[$this->section_name]['permissions'])) {
                    $this->theme->set_message(lang('permission-error-msg'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'forum');
                }
            }
        }
        if ($action == "edit") {
            if (isset($this->session->userdata[$this->section_name]['permissions']) && !empty($this->session->userdata[$this->section_name]['permissions'])) {
                if (!in_array('admin.forum.action.edit', $this->session->userdata[$this->section_name]['permissions'])) {
                    $this->theme->set_message(lang('permission-error-msg'), 'error');
                    redirect(BASE_ADMIN_URL_CUSTOM . 'forum');
                }
            }
        }
        //Type Casting
        $action = trim(strip_tags($action));
        $language_code = trim(strip_tags($language_code));
        $id = trim(strip_tags($id));

        // echo $action; exit;
        //Initialize
        $data = array();
        $categories = array();
        $is_draft = "";
        $is_sticky = "";

        //language parameters
        $language_list = $this->languages_model->get_languages();
        $language_code = strip_tags($language_code);
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }
        //pr($language_code);
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        //pr($language_detail);
        $language_id = $language_detail[0]['l']['id'];

        // Logic
        $categories = $this->forum_category_model->get_category_listing($language_id);

        // echo "<pre>"; print_r($categories); exit;
        if ($action == "edit") {
            $this->forum_post_model->id = $id;
            $forum_data = $this->forum_post_model->get_forum_listing("", $language_id);
            $data['id'] = $id;
        }

        if ($this->input->post('mysubmit')) {

            $data = $this->input->post();
            $id = intval($data['id']);

            //Variable Assignment
            $forum_title = trim(strip_tags($data['forum_title']));
            $forum_description = html_entity_decode(trim($data['forum_description']));
            $forum_category = trim(strip_tags($data['forum_category']));
            // $is_private = trim(strip_tags($data['is_private']));
            $slug_url = trim(strip_tags($data['slug_url']));
            $industry_id = trim(strip_tags($data['industry_id']));
            $sector_id = trim(strip_tags($data['sector_id']));
            $tag_id = isset($data['tag_id']) ? $data['tag_id'] : "";
            $is_draft = $data['is_draft'];
            $is_sticky = $data['is_sticky'];

            // field name, error message, validation rules

            $this->form_validation->set_rules('forum_title', lang('forum_title'), 'trim|required');
            $this->form_validation->set_rules('forum_description', lang('forum_description'), 'trim');
            // $this->form_validation->set_rules('slug_url', 'Slug URL', 'trim|required|callback_check_unique_slug');
            $this->form_validation->set_rules('slug_url', 'Slug URL', 'trim|required');

            if ($this->form_validation->run($this) == TRUE) {
                $data_array['forum_post_title'] = $forum_title;
                $data_array['slug_url'] = $slug_url;
                $data_array['forum_post_text'] = $forum_description;
                $data_array['category_id'] = $forum_category;
                // $data_array['is_private'] = $is_private;
                $data_array['lang_id'] = $language_id;
                $data_array['industry_id'] = $industry_id;
                $data_array['sector_id'] = $sector_id;
                $data_array['is_draft'] = $is_draft;
                $data_array['is_sticky'] = $is_sticky;



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
                redirect(BASE_ADMIN_URL_CUSTOM . 'forum/forum_listing/' . $forum_category . "/" . $language_code);
                exit;
            } else {
                $this->theme->set_message(lang('slug_exist'), 'error');
            }
        } else {
            //Variable Assignment
            $forum_title = "";
            $forum_description = "";
            $forum_category = "";
        }

        //  Pass data to view file
        if ($action == "edit") {

            $ForumTags = $this->forum_post_model->getForumTags($forum_data[0]['forum_post']['id']);
            // echo "<pre>"; print_r($ForumTags); exit;
            //Variable assignments to view
            $data['forum_name'] = $forum_data[0]['forum_post']['forum_post_title'];
            $data['status'] = $forum_data[0]['forum_post']['status'];
            $data['forum_description'] = $forum_data[0]['forum_post']['forum_post_text'];
            // $data['is_private'] = $forum_data[0]['forum_post']['is_private'];
            $data['forum_category'] = $forum_data[0]['forum_post']['category_id'];
            $data['slug_url'] = $forum_data[0]['forum_post']['slug_url'];
            $data['industry'] = $forum_data[0]['forum_post']['industry_id'];
            $data['sector'] = $forum_data[0]['forum_post']['sector_id'];
            $data['tags'] = $ForumTags;
            $is_draft = $forum_data[0]['forum_post']['is_draft'];
            $is_sticky = $forum_data[0]['forum_post']['is_sticky'];

            $industry = $forum_data[0]['forum_post']['industry_id'];
            $sector = $forum_data[0]['forum_post']['sector_id'];
            $tags = $ForumTags;
        }

        //Variable assignments to view
        $data['categories'] = $categories;
        $data['action'] = $action;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;
        $data['industry_list'] = $industry_list;
        $data['sector_list'] = $sector_list;
        $data['tag_list'] = $tag_list;
        $data['industry'] = $industry;
        $data['sector'] = $sector;
        $data['tags'] = $tags;
        $data['is_draft'] = $is_draft;
        $data['is_sticky'] = $is_sticky;

        //create breadcrumbs & page-title
        $this->breadcrumb->add(lang('forum_category'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
        if ($id == 0) {
            $status = 1;
            $this->theme->set('page_title', lang('add-forum'));
            $this->breadcrumb->add(lang('add-forum'));
        } else {
            $status = $data['status'];
            $this->theme->set('page_title', lang('edit-forum'));
            $this->breadcrumb->add(lang('edit-forum'));
        }

        //Render view
        $this->theme->view($data, 'admin_action');
    }

    /**
     * Function delete_forum for delete Forum.
     */
    function delete_forum() {
        //Initialize
        $data = array();
        $data = $this->input->post();

        //Type Casting
        $id = intval($data['id']);

        //Logic
        $this->forum_post_model->id = $id;
        $result = $this->forum_post_model->get_forum_listing($data['category']);

        if (!empty($result)) {
            $res = $this->forum_post_model->delete_forum($id);
            $this->forum_topics_model->post_id = $id;
            $res1 = $this->forum_topics_model->delete_topic();
            if ($res) {
                echo $this->theme->message(lang('forum-delete-success'), 'success');
            }
        } else {
            echo $this->theme->message(lang('invalid-id-msg'), 'error');
        }
    }

    /**
     * Function delete_topic for delete topic.
     */
    function delete_topic() {
        //Initialize
        $data = array();

        //Logic
        $data = $this->input->post();

        //Type Casting
        $id = intval($data['id']);
        $this->forum_topics_model->id = $id;
        $res = $this->forum_topics_model->delete_topic();
        if ($res) {
            echo $this->theme->message(lang('forum-topic-delete-success'), 'success');
        } else {
            echo $this->theme->message(lang('invalid-id-msg'), 'error');
        }
    }

    /**
     * Function forum_listing for display Forum of selected category.
     */
    /*public function forum_listing($category, $language_code = '') {
        //Type Casting
        $category = trim(strip_tags($category));
        $language_code = trim(strip_tags($language_code));

        // language code
        $language_list = $this->languages_model->get_languages();
        $language_code = strip_tags($language_code);
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        $language_id = $language_detail[0]['l']['id'];

        //Variable assignments to view
        $data['category_id'] = $category;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;

        //Create page-title
        $this->theme->set('page_title', lang('forum'));

        //Render view
        $this->breadcrumb->add(lang('forum-listing'));
        $this->theme->view($data, 'admin_forum_listing');
    }*/

    /**
     * Function ajax_forum_listing for fetching forum_listing page view.
     */
    public function forum_listing($category) {
        //Type Casting
        $category = trim(strip_tags($category));

        //Load data for url listing
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;

        //set sort/search parameters in pagging
        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['search_term'])) {
                $this->forum_post_model->search_term = $data['search_term'];
                $this->session->set_custom_userdata($this->section_name, "search_term", $this->forum_post_model->search_term);
            } else {
                $this->session->set_custom_userdata($this->section_name, "search_term", "");
            }

            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->forum_post_model->sort_by = $data['sort_by'];
                $this->forum_post_model->sort_order = $data['sort_order'];
            }

            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->forum_post_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-delete-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->forum_post_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->forum_post_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->forum_post_model->active_all_records()) {
                    echo $this->theme->message(lang('forum-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->forum_post_model->inactive_all_records()) {
                    echo $this->theme->message(lang('forum-inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['search_term'])) {
            $this->forum_post_model->search_term = $this->session->userdata[$this->section_name]['search_term'];
        }

        //logic
        $forums = $this->forum_post_model->get_forum_listing($category);
        $category_name = $this->forum_category_model->get_category_from_id($category);


        //Variable assignments to view
        if (!empty($category_name)) {
            $data['category'] = $category_name;
        }

        $data['forums'] = $forums;
        $data['category_id'] = $category;
        $data['page_number'] = $this->page_number;
        $this->forum_post_model->category_id = $category;
        $this->forum_post_model->_record_count = true;
        $total_records = $this->forum_post_model->get_forum_listing($category);
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->forum_post_model->search_term;
        $data['sort_by'] = $this->forum_post_model->sort_by;
        $data['sort_order'] = $this->forum_post_model->sort_order;
        foreach ($forums as $k => $v) {
            $this->forum_topics_model->id = $v['forum_post']['id'];
            $data["forums"][$k]["forum_post"]['rly_count'] = $this->forum_topics_model->record_count();
        }
        $this->breadcrumb->add(lang('forum_category'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
        $this->breadcrumb->add(lang('forum'));
        //Render view
        $this->theme->view($data);
    }

    function archive() {
        $data = $this->input->post();

        if (isset($data['type']) && $data['type'] == 'active') {
            if ($this->forum_post_model->active_records($data['ids'])) {
                echo $this->theme->message(lang('forum-active-success'), 'success');
                exit;
            }
        }

        if (isset($data['type']) && $data['type'] == 'archive') {
            if ($this->forum_post_model->inactive_records($data['ids'])) {
                echo $this->theme->message(lang('forum-archive-success'), 'success');
                exit;
            }
        }
    }

    /**
     * Function forum_post for display Forum Posts.
     */
    public function forum_post($id) {


        $forum_name = "";
        $language_id = 1;
        $id = trim(strip_tags($id));
        //Initialize
        $data = array();
        $data_array = array();

        //Load data for url listing
        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_topics_model->record_per_page = $this->record_per_page;
        $this->forum_topics_model->offset = $offset;

        // Logic
        $uri = mysql_real_escape_string($_SERVER['REQUEST_URI']);
        // echo $uri; exit;
        $view_count = $this->forum_activity_log_model->get_view_count($uri);

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();

            //Variable Assignment
            $topic_title = trim(strip_tags($data['topic_title']));
            $forum_name = trim(strip_tags($data['topic_title']));
            $topic_text = html_entity_decode(trim($data['topic_text']));

            $this->form_validation->set_rules('topic_title', lang('topic_title'), 'trim|required');
            $this->form_validation->set_rules('topic_text', lang('topic_text'), 'trim|required');

            if ($this->form_validation->run($this)) {
                $data_array['post_id'] = $id;
                $data_array['id'] = 0;
                $data_id['id'] = $id;
                $data_array['topic_title'] = $topic_title;
                $data_array['topic_text'] = $topic_text;
                $data_array['lang_id'] = $language_id;
                $data_array['created_by'] = $this->session->userdata[$this->theme->get('section_name')]['user_id'];

                //Type Casting
                $forum_title = trim(strip_tags($data['topic_title']));

                if (isset($data_array['topic_title']) && $data_array['topic_title'] != '') {
                    $this->forum_post_model->updated_date($data_id, $language_id);
                    $this->forum_topics_model->add_reply($data_array);
                    $this->theme->set_message('Reply successfully added', 'success');
                }

                redirect(current_url());
                // redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->theme->set_message(lang('fill-fields'), 'error');
                // redirect(current_url());
            }
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            if (isset($data['type']) && $data['type'] == 'delete') {
                if ($this->forum_topics_model->delete_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-delete-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active') {
                if ($this->forum_topics_model->active_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                if ($this->forum_topics_model->inactive_records($data['ids'])) {
                    echo $this->theme->message(lang('forum-inactive-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'active_all') {
                if ($this->forum_topics_model->active_all_records()) {
                    echo $this->theme->message(lang('forum-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->forum_topics_model->inactive_all_records()) {
                    echo $this->theme->message(lang('forum-inactive-success'), 'success');
                    exit;
                }
            }
        }

        $forum_first_post = $this->forum_post_model->forum_post_by_id($id, $language_id);
        $forum_post_comments = $this->forum_topics_model->get_topics_from_post($id, $language_id);
        $last_post = $this->forum_topics_model->last_update_datetime($id);

        //Variable assignments to view
        $data['forum_name'] = $forum_name;
        $data['forum_first_post'] = $forum_first_post;
        $data['forum_post_comments'] = $forum_post_comments;
        $data['id'] = $id;
        $data['last_post'] = $last_post;
        $data['view_count'] = $view_count;
        $data['page_number'] = $this->page_number;
        $this->forum_topics_model->id = $id;
        $this->forum_topics_model->_record_count = true;
        $total_records = $this->forum_topics_model->get_topics_from_post($id, $language_id);
        $data['total_records'] = $total_records;
        $data['search_term'] = $this->forum_topics_model->search_term;
        $data['sort_by'] = $this->forum_topics_model->sort_by;
        $data['sort_order'] = $this->forum_topics_model->sort_order;
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;

        //create breadcrumbs & page-title
        $this->theme->set('page_title', lang('forum-topics'));
        $this->breadcrumb->add(lang('forum-topics'));

        //Render view
        $this->theme->view($data, 'admin_forum_post');
    }

    /**
     * Function topic_edit for edit Forum topic.
     */
    public function topic_edit($id, $post_id) {
        //Type Casting
        $post_id = trim(strip_tags($post_id));
        $id = trim(strip_tags($id));

        //Initialize
        $data = array();
        $data_array = array();

        // language code

        // Logic
        $result = $this->forum_topics_model->get_record_by_id($id);
        if ($this->input->post()) {
            $data = $this->input->post();
            $topic_title = trim(strip_tags($data['topic_title']));
            $topic_text = html_entity_decode(trim($data['topic_text']));
            $data_array['id'] = $id;
            $data_id['id'] = $id;
            $data_array['topic_title'] = $topic_title;
            $data_array['topic_text'] = $topic_text;
            $data_array['status'] = $data['status'];

            //Variable Assignment
            $forum_title = trim(strip_tags($data['topic_title']));

            // field name, error message, validation rules
            $this->form_validation->set_rules('topic_title', lang('topic_title'), 'trim|required');
            $this->form_validation->set_rules('topic_text', lang('topic_text'), 'trim|required');


            //pre($data['file_exist']);
            if ($this->form_validation->run($this)) {
                if (isset($data_array['topic_title']) && $data_array['topic_title'] != '') {
                    $this->forum_post_model->updated_date($data_id);
                    $this->forum_topics_model->add_reply($data_array);

                    $this->theme->set_message(lang('topic-edited'), 'success');

                    redirect(BASE_ADMIN_URL_CUSTOM . 'forum/forum_post/' . $post_id);
                    exit;
                }
            } else {
                $this->theme->set_message(lang('fill-fields'), 'error');
            }
        }

        //Variable assignments to view
        $data['topic_title'] = $result['forum_topics']['topic_title'];
        $data['topic_text'] = $result['forum_topics']['topic_text'];
        $data['status'] = $result['forum_topics']['status'];
        $data['attach'] = $result['forum_topics']['attachment'];
        $data['post_id'] = $post_id;
        $data['language_id'] = $language_id;
        $this->theme->set('page_title', 'Edit Discussion Topics');

        //create breadcrumbs & page-title
        $this->breadcrumb->add(lang('edit-forum-topics'));

        //Render view
        $this->theme->view($data, 'admin_topic_edit');
    }

    /**
     * Function check_unique_slug for check is slug unique?
     */
    public function check_unique_slug() {
        $slug_url = $this->input->post('slug_url');
        $post_id = $this->input->post('id');

        //Type Casting
        $slug_url = trim(strip_tags($slug_url));

        //Logic
        $result = $this->forum_post_model->check_unique_slug($slug_url, $post_id);

        if (count($result) > 0) {
            $this->form_validation->set_message('check_unique_slug', lang('msg_available_slug_url'));
            //$this->form_validation->set_message('slug_url', lang('msg_available_slug_url'));
            return false;
        } else {
            return true;
        }
    }

    /*public function view_data($id = 0, $lang) {
        $result = $this->forum_post_model->forum_post_by_id($id, "");

        //Initialize
        $data = array();

        //Variable assignments to view
        $data = $result;
        $data['lang'] = $lang;
        $data['id'] = $id;

        //Render view
        $this->theme->view($data);
    }*/

    function view_data($id = 0, $language_code = '') {
        $language_code = trim(strip_tags($language_code));
        $id = trim(strip_tags($id));

        //Initialize
        $data = array();
        $categories = array();
        $is_draft = "";
        $is_sticky = "";

        //language parameters
        $language_list = $this->languages_model->get_languages();
        $language_code = strip_tags($language_code);
        if ($language_code == '') {
            $language_code = $this->session->userdata[$this->section_name]['site_lang_code'];
        }
        //pr($language_code);
        $language_detail = $this->languages_model->get_languages_by_code($language_code);
        //pr($language_detail);
        $language_id = $language_detail[0]['l']['id'];

        // Logic
        $categories = $this->forum_category_model->get_category_listing($language_id);

        // echo "<pre>"; print_r($categories); exit;
    
        $this->forum_post_model->id = $id;
        $forum_data = $this->forum_post_model->get_forum_listing("", $language_id);
        $data['id'] = $id;
        

        $forum_title = "";
        $forum_description = "";
        $forum_category = "";

        //  Pass data to view file
        $ForumTags = $this->forum_post_model->getForumTags($forum_data[0]['forum_post']['id']);
        // echo "<pre>"; print_r($ForumTags); exit;
        //Variable assignments to view
        $data['forum_name'] = $forum_data[0]['forum_post']['forum_post_title'];
        $data['status'] = $forum_data[0]['forum_post']['status'];
        $data['forum_description'] = $forum_data[0]['forum_post']['forum_post_text'];
        // $data['is_private'] = $forum_data[0]['forum_post']['is_private'];
        $data['forum_category'] = $forum_data[0]['forum_post']['category_id'];
        $data['slug_url'] = $forum_data[0]['forum_post']['slug_url'];
        $data['industry'] = $forum_data[0]['forum_post']['industry_id'];
        $data['sector'] = $forum_data[0]['forum_post']['sector_id'];
        $data['tags'] = $ForumTags;
        $is_draft = $forum_data[0]['forum_post']['is_draft'];
        $is_sticky = $forum_data[0]['forum_post']['is_sticky'];

        $industry = $forum_data[0]['forum_post']['industry_id'];
        $sector = $forum_data[0]['forum_post']['sector_id'];
        $tags = $ForumTags;
        

        //Variable assignments to view
        $data['categories'] = $categories;
        $data['language_code'] = $language_detail[0]['l']['language_code'];
        $data['language_name'] = $language_detail[0]['l']['language_name'];
        $data['language_id'] = $language_id;
        $data['languages'] = $language_list;
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        $data['languages'] = $language_list;
        $data['industry_list'] = $industry_list;
        $data['sector_list'] = $sector_list;
        $data['tag_list'] = $tag_list;
        $data['industry'] = $industry;
        $data['sector'] = $sector;
        $data['tags'] = $tags;
        $data['is_draft'] = $is_draft;
        $data['is_sticky'] = $is_sticky;

        $this->breadcrumb->add(lang('forum'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum');
        $this->breadcrumb->add(lang('view-forum'));

        //Render view
        $this->theme->view($data);
    }

    function moderator_list() {

        //Paging parameters

        $offset = get_offset($this->page_number, $this->record_per_page);
        $this->forum_post_model->record_per_page = $this->record_per_page;
        $this->forum_post_model->offset = $offset;


        if ($this->input->post()) {
            $data = $this->input->post();

            if (empty($data['page_number'])) {
                $this->session->set_custom_userdata($this->section_name, "moderator_offset", "");
                $this->session->set_custom_userdata($this->section_name, "moderator_page_number", "");
            }

            if (isset($data['search_term'])) {
                $this->forum_post_model->projects_search_term = trim($data['search_term']);
                $this->session->set_custom_userdata($this->section_name, "moderator_search_term", $this->input->post('search_term'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "moderator_search_term", "");
            }

            if (isset($data['sort_by']) && $data['sort_order']) {
                $this->forum_post_model->projects_sort_by = $data['sort_by'];
                $this->forum_post_model->projects_sort_order = $data['sort_order'];
                $this->session->set_custom_userdata($this->section_name, "moderator_sort_by", $this->input->post('sort_by'));
                $this->session->set_custom_userdata($this->section_name, "moderator_sort_order", $this->input->post('sort_order'));
            } else {
                $this->session->set_custom_userdata($this->section_name, "moderator_sort_by", "");
                $this->session->set_custom_userdata($this->section_name, "moderator_sort_order", "");
            }

            if (isset($data['type']) && $data['type'] == 'delete') {

                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = base64_decode($val);
                }
                // Newly added
                if ($this->users_model->delete_records($tempArr)) {
                    echo $this->theme->message(lang('mod-delete-success'), 'success');
                    exit;
                }
            }

            if (isset($data['type']) && $data['type'] == 'active') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = base64_decode($val);
                }
                // Newly added
                if ($this->users_model->active_records($tempArr)) {
                    echo $this->theme->message(lang('mod-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive') {
                // Newly added
                $tempArr = array();
                foreach ($data['ids'] as $key => $val) {
                    $tempArr[] = base64_decode($val);
                }
                // Newly added
                if ($this->users_model->inactive_records($tempArr)) {
                    echo $this->theme->message(lang('mod-inactive-success'), 'success');
                    exit;
                }
            }

            if (isset($data['type']) && $data['type'] == 'active_all') {

                if ($this->users_model->active_all_records('4')) {
                    echo $this->theme->message(lang('mod-active-success'), 'success');
                    exit;
                }
            }
            if (isset($data['type']) && $data['type'] == 'inactive_all') {
                if ($this->users_model->inactive_all_records('4')) {
                    echo $this->theme->message(lang('mod-inactive-success'), 'success');
                    exit;
                }
            }
        }

        if (!empty($this->session->userdata[$this->section_name]['moderator_search_term'])) {
            $this->forum_post_model->moderator_search_term = trim($this->session->userdata[$this->section_name]['moderator_search_term']);
        }
        if (!empty($this->session->userdata[$this->section_name]['moderator_sort_by'])) {
            $this->forum_post_model->moderator_sort_by = $this->session->userdata[$this->section_name]['moderator_sort_by'];
        }
        if (!empty($this->session->userdata[$this->section_name]['moderator_sort_order'])) {
            $this->forum_post_model->moderator_sort_order = $this->session->userdata[$this->section_name]['moderator_sort_order'];
        }
        if (!empty($this->session->userdata[$this->section_name]['moderator_offset'])) {
            $this->forum_post_model->moderator_offset = $this->session->userdata[$this->section_name]['moderator_offset'];
        }
        if (!empty($this->session->userdata[$this->section_name]['moderator_page_number'])) {
            $this->forum_post_model->moderator_page_number = $this->session->userdata[$this->section_name]['moderator_page_number'];
        }

        //Load data for url listing
        $users = $this->forum_post_model->get_moderator_listing();
        $this->forum_post_model->_record_count = true;
        $total_records = $this->forum_post_model->get_moderator_listing();
        // Pass data to view file

        $data['users'] = $users;
        $data['search_term'] = $this->forum_post_model->moderator_search_term;
        $data['sort_by'] = $this->forum_post_model->moderator_sort_by;
        $data['sort_order'] = $this->forum_post_model->moderator_sort_order;
        $data['page_number'] = $this->page_number;
        $data['total_records'] = $total_records;

        $this->theme->set('page_title', lang('moderators'));
        $this->breadcrumb->add(lang('moderators'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
        $this->theme->view($data);
    }

    function action_moderator($id = 0) {

        $firstname = "";
        $lastname = "";
        $email = "";
        $username = "";
        $password = "";
        $status = "";

        if ($id != '' || $id != 0) {
            $moderator = $this->forum_post_model->get_moderator($id);

            $firstname = $moderator['firstname'];
            $lastname = $moderator['lastname'];
            $email = $moderator['email'];
            $username = $moderator['username'];
            $status = $moderator['status'];
        }

        if ($this->input->post('mysubmit')) {
            $data = $this->input->post();
           
            $id = intval($data['id']);

            $post_array = array(
                'role_id' => 5,
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'username' => $data['username'],
                'status' => $data['status']
            );

            $this->members_validation_rules();

            if ($this->form_validation->run($this)) {
                if ($id == "" || $id == 0) {
                    $post_array['password'] = encriptsha1($data['password']);
                    $this->forum_post_model->insert_moderator($post_array);
                } else {

                    $post_array['id'] = $data['id'];
                    if (!empty($data['password'])) {
                        $post_array['password'] = encriptsha1($data['password']);
                    }

                    $this->forum_post_model->update_moderator($post_array);
                }

                $this->theme->set_message(lang('mod-save-success'), 'success');
                redirect(BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
                exit;
            } else {
                $data_array = $this->input->post();

                $firstname = $data_array['firstname'];
                $lastname = $data_array['lastname'];
                $email = $data_array['email'];
                $username = $data_array['username'];
                $status = $data_array['status'];
                $id = $data_array['id'];
            }
        }

        $data = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'status' => $status,
            'id' => $id
        );

        $this->breadcrumb->add(lang('moderators'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
        if($id > 0){
            $this->theme->set('page_title', lang('edit-moderator'));
            $this->breadcrumb->add(lang('edit-moderator'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
        }else{
            $this->theme->set('page_title', lang('add-moderator'));
            $this->breadcrumb->add(lang('add-moderator'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
        }
        
        $this->theme->view($data);
    }

    function members_validation_rules() {
        $this->form_validation->set_rules('firstname', lang('first-name'), 'trim|required|min_length[2]');
        $this->form_validation->set_rules('lastname', lang('last-name'), 'trim|required|min_length[2]');
        $this->form_validation->set_rules('username', lang('username'), 'trim|required|min_length[2]');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email|callback_check_unique_email');
    }

    public function check_unique_email() {
        $data = $this->input->post();

        $result = $this->users_model->check_unique_email($data);

        if ($result > 0) {
            $this->form_validation->set_message('check_unique_email', lang('msg-alvailable-email'));
            return false;
        } else {
            return true;
        }
    }

    function delete() {
        if ($this->check_permission()) {
            $data = $this->input->post();
            //$id = intval($data['id']);
            $id = intval(base64_decode($data['id']));

            $result = $this->users_model->get_user_detail($id);

            if ($id == 1) {
                echo $this->theme->message(lang('invalid-id-msg'), 'error');
                exit;
            }

            if (!empty($result)) {
                $this->users_model->delete_user($id);
                echo $this->theme->message(lang('mod-delete-success'), 'success');
                //redirect($this->section_name . '/forum/moderator_list/');
            }
        } else {
            $this->theme->set_message(lang('permission-not-allowed'), 'error');
            redirect(BASE_ADMIN_URL_CUSTOM . 'users');
            exit;
        }
    }

    function set_permission($id = 0) {

        /**
         * @Permissions to get
         */
        $perm_list = $this->forum_post_model->getPermList();
        $SetPerm = $this->forum_post_model->getSetPerm($id);

        //echo "<pre>"; print_r($SetPerm); exit;


        $data = array(
            'id' => $id,
            'records' => $perm_list,
            'SetPerm' => $SetPerm
        );

        /**
         * @View details
         */
        $this->theme->set('page_title', lang('mod-permit'));
        $this->breadcrumb->add(lang('moderators'), base_url() . BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
        $this->breadcrumb->add(lang('permissions'));
        $this->theme->view($data);
    }

    function save_permission() {
        $data = $this->input->post();
        $this->forum_post_model->save_permission($data);
        $this->theme->set_message(lang('permission-saved-success'), 'success');
        redirect(BASE_ADMIN_URL_CUSTOM . 'forum/moderator_list');
    }

}
