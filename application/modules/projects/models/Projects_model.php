<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class Projects_model extends Base_Model {



    protected $_tbl_projects = 'project_info';

    protected $_tbl_projects_categories = 'project_categories';

    protected $_tbl_projects_todos = 'project_to_do_info';

    protected $_tbl_projects_todo_comments = 'project_todo_comments';

    protected $_tbl_projects_todo_files = 'project_todo_files';

    protected $_tbl_projects_comments = 'project_comments_info';

    protected $_tbl_action_plan = 'action_plan';

    protected $_tbl_sites = TBL_SITES;

    protected $_tbl_hotels = TBL_HOTELS;

    protected $_tbl_users = TBL_USERS;

    protected $_tbl_regions = 'regions';

    public $projects_search_term = "";

    public $projects_sort_by = "";

    public $filter_site_id = '';

    public $filter_category_id = '';

    public $filter_admin_site_id = false;

    public $projects_sort_order = "";

    public $categories_search_term = "";

    public $categories_sort_by = "";

    public $categories_sort_order = "";

    public $user_id = "";

    public $role_id = "";

    public $site_id = "";



    function __construct() {

        parent::__construct();

    }



    function getProjects($site_id = 0, $role_id = 0, $category_id = 0) {

        if (isset($this->projects_search_term) && $this->projects_search_term != "") {

            $this->db->like("LOWER(p.project_name)", strtolower($this->projects_search_term));

        }

        if ($role_id == 1 || $role_id == 2) {

            $this->db->order_by("pc.name", "ASC");

            $this->db->order_by("p.project_name", "ASC");

            $this->db->order_by("pdi.todo_name", "ASC");

        } else {

            if (isset($this->projects_sort_by) && $this->projects_sort_by != "" && $this->projects_sort_order != "") {

                $this->db->order_by('p.' . $this->projects_sort_by, $this->projects_sort_order);

            }

        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {

            $this->db->limit($this->record_per_page, $this->offset);

        }



        if (!empty($this->filter_category_id)) {

            $this->db->where('p.project_category_id', $this->filter_category_id);

        }



        if (!empty($this->filter_region_id)) {

            $this->db->where('r.id', $this->filter_region_id);

        }



        if (!empty($this->filter_color_id)) {

            $this->db->where('pdi.todo_color', $this->filter_color_id);

        }



        if ($this->filter_admin_site_id) {

            if ($this->filter_site_id != "" && $this->filter_site_id != "0" && $role_id != 1 && $role_id != 2) {

                $this->db->where('p.site_id', $this->filter_site_id);

            }

        } else {

            if ($role_id != 1 && $role_id != 2) {

                $this->db->where('p.site_id', $site_id);

            } else {

                $site_id_array = array();

                if (!empty($site_id)) {

                    $site_id_array[] = $site_id;

                    //$this->db->where('p.site_id', $site_id);

                }

                $site_id_array[] = 0;

                $this->db->where_in('p.site_id', $site_id_array);

            }

        }



        if ($this->filter_site_id != "" && $this->filter_site_id != "0" && ($role_id == 1 || $role_id == 2)) {

            $this->db->select('p.*,pc.name,h.hotel_name,s.site_location_name,ap.site_id,aps.site_location_name, pdi.todo_color as ema_todo_color, pdi.todo_name as ema_todo_name, pdi.id as pdi_id, aps.id as aps_id');

        } else if ($role_id == 1 || $role_id == 2) {

            $this->db->select('distinct(ap.site_id), p.*,pc.name,h.hotel_name,s.site_location_name,aps.site_location_name, pdi.todo_color as ema_todo_color, pdi.todo_name as ema_todo_name, pdi.id as pdi_id, aps.id as aps_id');

        } else {

            $this->db->select('p.*,pc.name,h.hotel_name,s.site_location_name');

        }

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' as pc', 'pc.id = p.project_category_id');

        $this->db->join($this->_tbl_hotels . ' as h', 'h.id = p.hotel_id');

        $this->db->join($this->_tbl_sites . ' as s', 's.id = p.site_id', 'LEFT');



        //if($this->filter_site_id != "" && $this->filter_site_id != "0" && ($role_id == 1 || $role_id == 2)) {

        if ($role_id == 1 || $role_id == 2) {

            $this->db->join($this->_tbl_projects_todos . ' as pdi', 'pdi.project_id = p.id', 'LEFT');

            $this->db->join($this->_tbl_action_plan . ' as ap', 'ap.project_to_do_id = pdi.id', 'LEFT');

            $this->db->join($this->_tbl_sites . ' as aps', 'aps.id = ap.site_id', 'LEFT');

            $this->db->join($this->_tbl_regions . ' as r', 'r.id = aps.region_id', 'LEFT');

            $this->db->where("ap.site_id IS NOT NULL");

            $this->db->where("pdi.status =1");

        } else {

            $this->db->join($this->_tbl_regions . ' as r', 'r.id = s.region_id', 'LEFT');

        }



        if ((int) $category_id > 0) {

            $this->db->where('p.project_category_id', $category_id);

        }



        $this->db->where('p.status !=', -1); // check project status should not be deleted

        $this->db->where('p.status !=', '0'); // check project status should not be inactive

        $this->db->where("(s.status IS NULL OR s.status = '1') "); // check site status should not be deleted

        //$this->db->where('s.status !=', '0'); // check site status should not be inactive



        if ($this->filter_site_id != "" && $this->filter_site_id != "0" && ($role_id == 1 || $role_id == 2)) {

            //$this->db->group_by("p.id");            

            $this->db->having("ap.site_id = " . $this->filter_site_id);

        }

        $result = $this->db->get();



        //pre($this->db->last_query());

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    function getPublicProjects($category_id = 0) {

        $this->db->select('p.*,pc.name,h.hotel_name');

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' as pc', 'pc.id = p.project_category_id');

        $this->db->join($this->_tbl_hotels . ' as h', 'h.id = p.hotel_id');



        //if((int)$category_id > 0){

        $this->db->where('p.project_category_id', $category_id);

        //}

        //$this->db->where('p.site_id', 0);

        $this->db->where('p.status !=', -1);

        $result = $this->db->get();

        return $this->db->custom_result($result);

    }



    function getActionCountForProject($project_id = 0) {

        $return = array();

        if ($project_id != 0) {

            $this->db->select('GROUP_CONCAT(a.id) as todo_ids');

            $this->db->from('project_to_do_info AS a');

            $this->db->where('project_id', $project_id);

            $result = $this->db->get()->row();



            if (!empty($result->todo_ids)) {

                $this->db->select('project_to_do_id');

                $this->db->from($this->_tbl_action_plan);

                $this->db->where('user_id', $this->user_id);

                $this->db->where('site_id', $this->site_id);

                $this->db->where_in('project_to_do_id', explode(',', $result->todo_ids));

                $results = $this->db->get();

                $result_array = $results->result_array();



                foreach ($result_array as $key => $value) {

                    $return[$value['project_to_do_id']] = $value;

                }

            }

        }



        return $return;

    }



    function get_todos($project_id) {

        $this->db->select('pd.*');

        $this->db->from($this->_tbl_projects_todos . ' AS pd');

        $this->db->join($this->_tbl_projects . ' as p', 'p.id = pd.project_id');

        $this->db->where('pd.project_id', $project_id);

        $this->db->where('pd.status != ', '-1');

        $this->db->where('p.status != ', '-1');

        $result = $this->db->get();

        return $result->result_array();

    }



    function get_projects_helper() {

        $this->db->select('p.id,p.project_name');

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->where('p.status !=', -1);



        $result = $this->db->get();

        $return = array();

        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $value) {

                $return[$value['id']] = $value['project_name'];

            }

        }



        return $return;

    }



    function getProject($id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects);

        $this->db->where('id =', $id);

        if (!in_array($this->role_id, array(1, 2))) {

            $this->db->where_in('site_id', array($this->site_id));

        }



        //$this->db->where_in('site_id', array(0,$this->site_id));



        $this->db->where('status !=', -1);

        $result = $this->db->get();

        //echo $this->db->last_query();exit;

        return $result->row_array();

    }



    function getProjectForActionplan($id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects);

        $this->db->where('id =', $id);

        if (!in_array($this->role_id, array(1, 2))) {

            $this->db->where_in('site_id', array(0, $this->site_id));

        }



        $this->db->where('status !=', -1);

        $result = $this->db->get();

        return $result->row_array();

    }



    function save_project($postdata = array()) {

        $data = array();

        $data['project_name'] = $postdata['project_name'];

        $data['project_description'] = $postdata['project_description'];

        $data['project_category_id'] = $postdata['project_category_id'];

        $data['hotel_id'] = $postdata['hotel_id'];

        $data['site_id'] = $postdata['site_id'];

        $data['start_date'] = date("Y-m-d H:i:s", strtotime($postdata['start_date']));

        $data['end_date'] = date("Y-m-d H:i:s", strtotime($postdata['end_date']));

        $data['status'] = $postdata['status'];



        $project = $this->getProject($postdata['id']);

        if (!empty($project)) {

            // Edit project

            $data['id'] = $postdata['id'];

            $data['modify_by'] = $postdata['user_id'];

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->where('id', $data['id']);

            $this->db->update($this->_tbl_projects, $data);

            $id = $data['id'];

        } else {

            // Add project

            $data['user_id'] = $postdata['user_id'];

            $data['modify_by'] = $postdata['user_id'];

            $data['created_by'] = $postdata['user_id'];

            $this->db->set('created_on', 'NOW()', FALSE);

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->insert($this->_tbl_projects, $data);

            $id = $this->db->insert_id();

        }

        return $id;

    }



    function save_project_todo($project_id, $todos, $section_name, $site_id = 0) {



        // Delete not posted data

        $this->db->where_not_in('id', implode(',', $todos['todo_id']));

        $this->db->where('project_id', $project_id);

        $this->db->set('status', -1);

        $this->db->update($this->_tbl_projects_todos);



        $data['project_id'] = $project_id;

        if ($site_id != "0") {

            $data['site_id'] = (isset($this->session->userdata[$section_name]['site_id'])) ? $this->session->userdata[$section_name]['site_id'] : 0;

        }

        $data['hotel_id'] = (isset($this->session->userdata[$section_name]['hotel_id'])) ? $this->session->userdata[$section_name]['hotel_id'] : 0;

        $data['user_id'] = (isset($this->session->userdata[$section_name]['user_id'])) ? $this->session->userdata[$section_name]['user_id'] : 0;

        $data['status'] = 1;

        if (!empty($todos['todo_id'])) {

            foreach ($todos['todo_id'] as $key => $todo) {

                $data['todo_name'] = $todos['todo_key'][$key];

                $data['todo_value'] = $todos['todo_value'][$key];

                $data['todo_color'] = $todos['todo_color'][$key];

                if (isset($todos['todo_image'][$key]) && !empty($todos['todo_image'][$key])) {

                    $data['todo_image'] = $todos['todo_image'][$key];

                } else {

                    unset($data['todo_image']);

                }



                if ($data['todo_name'] != '') {

                    $checked = false;

                    $this->db->select('*');

                    $this->db->from($this->_tbl_projects_todos);

                    $this->db->where('id', $todo);

                    $this->db->where('project_id', $project_id);

                    $result = $this->db->get();

                    if ($result->num_rows() > 0) {

                        $checked = true;

                    }



                    if (!empty($todo) && $checked) {

                        // Add new TODO

                        $this->db->where('id', $todo);

                        $this->db->set('modify_on', 'NOW()', FALSE);

                        $this->db->update($this->_tbl_projects_todos, $data);

                    } else {

                        // Update TODO

                        $this->db->set('created_on', 'NOW()', FALSE);

                        $this->db->set('modify_on', 'NOW()', FALSE);

                        $this->db->insert($this->_tbl_projects_todos, $data);

                    }

                }

            }

        }

    }



    function getCategories() {

        if (isset($this->categories_search_term) && $this->categories_search_term != "") {

            $this->db->like("LOWER(pc.name)", strtolower($this->categories_search_term));

        }

        if (isset($this->categories_sort_by) && $this->categories_sort_by != "" && $this->categories_sort_order != "") {

            $this->db->order_by('pc.' . $this->categories_sort_by, $this->categories_sort_order);

        }

        if (isset($this->categories_record_per_page) && isset($this->categories_offset) && !isset($this->_record_count) && $this->_record_count != true) {

            $this->db->limit($this->categories_record_per_page, $this->categories_offset);

        }



        $this->db->select('*');

        $this->db->from($this->_tbl_projects_categories . ' as pc');

        $this->db->where('pc.status !=', -1);

        $result = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    // For only active categories

    function getCategoriesList() {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects_categories . ' as pc');

        $this->db->where('pc.status', 1);

        $result = $this->db->get();

        return $this->db->custom_result($result);

    }



    function get_categories_helper() {

        $this->db->select('pc.id,pc.name');

        $this->db->from($this->_tbl_projects_categories . ' as pc');

        $this->db->where('pc.status', 1);



        $result = $this->db->get();

        $return = array();

        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $value) {

                $return[$value['id']] = $value['name'];

            }

        }



        return $return;

    }



    function get_regions_helper() {

        $this->db->select('r.id,r.region_name');

        $this->db->from($this->_tbl_regions . ' as r');

        $this->db->where('r.status', 1);



        $result = $this->db->get();

        $return = array();

        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $value) {

                $return[$value['id']] = $value['region_name'];

            }

        }



        return $return;

    }



    function getCategory($id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects_categories);

        $this->db->where('id =', $id);

        $this->db->where('status !=', -1);

        $result = $this->db->get();

        return $result->row_array();

    }



    function save_category($postdata = array()) {

        $data = array();

        $data['name'] = $postdata['name'];

        $data['description'] = $postdata['description'];

        $data['status'] = $postdata['status'];



        $category = $this->getCategory($postdata['id']);

        if (!empty($category)) {

            // Edit category

            $data['id'] = $postdata['id'];

            $data['modify_by'] = $postdata['user_id'];

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->where('id', $data['id']);

            $this->db->update($this->_tbl_projects_categories, $data);

            $id = $data['id'];

        } else {

            // Add category

            $data['modify_by'] = $postdata['user_id'];

            $data['created_by'] = $postdata['user_id'];

            $this->db->set('created_on', 'NOW()', FALSE);

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->insert($this->_tbl_projects_categories, $data);

            $id = $this->db->insert_id();

        }

        return $id;

    }



    // project actions

    public function inactive_records($id = array()) {

        $this->db->set('status', 0);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects);



        return true;

    }



    public function inactive_all_records() {

        $this->db->set('status', 0);

        $this->db->where('status !=', -1);



        if ($this->role_id != 1) {

            $this->db->where('site_id', $this->site_id);

        } else {

            if (!empty($this->site_id)) {

                $this->db->where('site_id', $this->site_id);

            }

        }



        $this->db->update($this->_tbl_projects);

        return true;

    }



    public function active_records($id = array()) {

        $this->db->set('status', 1);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects);



        return true;

    }



    public function active_all_records() {

        $this->db->set('status', 1);

        $this->db->where('status !=', -1);



        if ($this->role_id != 1) {

            $this->db->where('site_id', $this->site_id);

        } else {

            if (!empty($this->site_id)) {

                $this->db->where('site_id', $this->site_id);

            }

        }



        $this->db->update($this->_tbl_projects);



        return true;

    }



    public function get_delete_todo_all_records($id = array(), $pid) {

        $this->db->select('id');

        $this->db->from($this->_tbl_projects_todos . ' as pdi');

        $this->db->where('site_id', '0');

        $this->db->where('status', '1');

        $this->db->where_not_in('id', $id);

        $this->db->where('project_id', $pid);

        $result = $this->db->get();

        return count($this->db->custom_result($result));

    }



    public function delete_todo_records($id = array(), $pid) {

        $pid = (int) $pid;

        if ($pid > 0) {

            $this->db->where_in('id', $pid);

            //$this->db->set('status', -1);

            //$this->db->set('deleted_by', $this->user_id , FALSE);

            //$this->db->set('deleted_on', 'NOW()', FALSE);

            //$this->db->update($this->_tbl_projects);

            $this->db->delete($this->_tbl_projects);

        }



        $this->db->where_in('id', $id);

        //$this->db->set('status', -1);

        //$this->db->set('deleted_by', $this->user_id, FALSE);

        //$this->db->set('deleted_on', 'NOW()', FALSE);

        //$this->db->update($this->_tbl_projects_todos);

        $this->db->delete($this->_tbl_projects_todos);

        return true;

    }



    public function delete_action_plan_records($id = array(), $acid = array()) {

        //pr($id);

        //pre($acid);

        $this->db->where_in('site_id', $id);

        $this->db->where_in('project_to_do_id', $acid);

        $this->db->delete($this->_tbl_action_plan);

        

        $this->db->where_in('site_id', $id);

        $this->db->where_in('project_todo_id', $acid);

        $this->db->delete($this->_tbl_projects_todo_comments);

        

        $this->db->where_in('site_id', $id);

        $this->db->where_in('project_todo_id', $acid);

        $this->db->delete($this->_tbl_projects_todo_files);

        return true;

    }



    public function delete_records($id = array()) {

        $this->db->where_in('id', $id);

        $this->db->set('status', -1);

        $this->db->set('deleted_by', $this->user_id, FALSE);

        $this->db->set('deleted_on', 'NOW()', FALSE);

        $this->db->update($this->_tbl_projects);

        return true;

    }



    // project actions

    // Category actions

    public function inactive_records_category($id = array()) {

        //Check projects for selected categories

        $this->db->select('pc.id');

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' AS pc', 'p.project_category_id = pc.id');

        $this->db->where('p.status !=', -1);

        $this->db->where_in('pc.id', $id);

        $this->db->group_by('pc.id');

        $result = $this->db->get();

        $projects_categories = $result->result_array();

        $categorycheck = false;



        if (!empty($projects_categories)) {

            $ids = array();

            foreach ($projects_categories as $value) {

                $ids[] = $value['id'];

            }



            foreach ($id as $value) {

                if (in_array($value, $ids)) {

                    $categorycheck = true;

                }

            }

        }



        if ($categorycheck) {

            return false;

        }



        $this->db->set('status', 0);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects_categories);



        return true;

    }



    public function inactive_all_records_category() {

        //Check projects for selected categories

        $this->db->select('pc.id');

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' AS pc', 'p.project_category_id = pc.id');

        $this->db->where('p.status !=', -1);

        $this->db->where('pc.status !=', -1);

        $this->db->group_by('pc.id');

        $result = $this->db->get();



        $projects_categories = $result->result_array();

        if (!empty($projects_categories)) {

            return false;

        }



        $this->db->set('status', 0);

        $this->db->where('status !=', -1);

        $this->db->update($this->_tbl_projects_categories);

        return true;

    }



    public function active_records_category($id = array()) {

        $this->db->set('status', 1);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects_categories);



        return true;

    }



    public function active_all_records_category() {

        $this->db->set('status', 1);

        $this->db->where('status !=', -1);

        $this->db->update($this->_tbl_projects_categories);



        return true;

    }



    public function delete_records_category($id = array()) {

        //Check projects for selected categories

        $this->db->select('pc.id');

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' AS pc', 'p.project_category_id = pc.id');

        $this->db->where('p.status !=', -1);

        $this->db->where_in('pc.id', $id);

        $this->db->group_by('pc.id');

        $result = $this->db->get();

        $projects_categories = $result->result_array();

        $categorycheck = false;



        if (!empty($projects_categories)) {

            $ids = array();

            foreach ($projects_categories as $value) {

                $ids[] = $value['id'];

            }



            foreach ($id as $value) {

                if (in_array($value, $ids)) {

                    $categorycheck = true;

                }

            }

        }



        if ($categorycheck) {

            return false;

        }



        $this->db->where_in('id', $id);

        $this->db->set('deleted_by', $this->user_id, FALSE);

        $this->db->set('deleted_on', 'NOW()', FALSE);

        $this->db->set('status', -1);

        $this->db->update($this->_tbl_projects_categories);

        return true;

    }



    // Category actions

    //Comments action

    function getComments($project_id = 0) {

        if (isset($this->search_term) && $this->search_term != "") {

            $this->db->like("LOWER(c.comments)", strtolower($this->search_term));

        }

        if (isset($this->sort_by) && $this->sort_by != "" && $this->sort_order != "") {

            $this->db->order_by('c.' . $this->sort_by, $this->sort_order);

        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {

            $this->db->limit($this->record_per_page, $this->offset);

        }



        $this->db->select('c.*,p.project_name,u.firstname,u.lastname,u.username');

        $this->db->join($this->_tbl_projects . ' as p', 'p.id = c.project_id');

        $this->db->join($this->_tbl_users . ' as u', 'u.id = c.user_id');

        $this->db->from($this->_tbl_projects_comments . ' as c');

        $this->db->where('c.status !=', -1);

        if ($project_id > 0) {

            $this->db->where('c.project_id', $project_id);

        }



        $result = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    function getComment($id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects_comments);

        $this->db->where('id =', $id);

        $result = $this->db->get();

        return $result->row_array();

    }



    function save_comment($postdata = array()) {

        $data = array();

        $data['comments'] = $postdata['comments'];

        $data['project_id'] = $postdata['project_id'];

        $data['hotel_id'] = $postdata['hotel_id'];

        $data['site_id'] = $postdata['site_id'];

        $data['status'] = $postdata['status'];



        $comment = $this->getComment($postdata['id']);

        if (!empty($comment)) {

            // Edit category

            $data['id'] = $postdata['id'];

            $data['modify_by'] = $postdata['user_id'];

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->where('id', $data['id']);

            $this->db->update($this->_tbl_projects_comments, $data);

            $id = $data['id'];

        } else {

            // Add category

            $data['user_id'] = $postdata['user_id'];

            $data['modify_by'] = $postdata['user_id'];

            $data['created_by'] = $postdata['user_id'];

            $this->db->set('created_on', 'NOW()', FALSE);

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->insert($this->_tbl_projects_comments, $data);

            $id = $this->db->insert_id();

        }

        return $id;

    }



    public function inactive_records_comment($id = array()) {

        $this->db->set('status', 0);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects_comments);



        return true;

    }



    public function inactive_all_records_comment() {

        $this->db->set('status', 0);

        $this->db->where('status !=', -1);

        $this->db->update($this->_tbl_projects_comments);

        return true;

    }



    public function active_records_comment($id = array()) {

        $this->db->set('status', 1);

        $this->db->where_in('id', $id);

        $this->db->update($this->_tbl_projects_comments);



        return true;

    }



    public function active_all_records_comment() {

        $this->db->set('status', 1);

        $this->db->where('status !=', -1);

        $this->db->update($this->_tbl_projects_comments);



        return true;

    }



    public function delete_records_comment($id = array()) {

        $this->db->where_in('id', $id);

        $this->db->set('status', -1);

        $this->db->set('deleted_by', $this->user_id, FALSE);

        $this->db->set('deleted_on', 'NOW()', FALSE);

        $this->db->update($this->_tbl_projects_comments);

        return true;

    }



    //Comments action



    function get_site_listing($site_id, $role_id) {

        $this->db->select('s.id,s.site_location_name');

        $this->db->from($this->_tbl_sites . ' AS s');

        $this->db->where('s.status', 1);



        $all_site_roles = array(1,2);

        if (!in_array($role_id, $all_site_roles)) {

            $this->db->where('s.id', $site_id);

        }

        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($query));

        } else {

            return $this->db->custom_result($query);

        }

    }



    function saveActionPlan($postdata = array()) {

        // pre($postdata);

        $this->db->select('a.project_to_do_id');

        $this->db->from($this->_tbl_action_plan . ' AS a');

        $this->db->where('user_id', $postdata['user_id']);

        $this->db->where('site_id', $postdata['site_id']);

        $this->db->where('project_to_do_id', $postdata['todo_id']);

        $record_count = $this->db->get()->num_rows();

        // pre($record_count);

        if ($record_count > 0) {

            $data['user_id'] = $postdata['user_id'];

            $data['site_id'] = $postdata['site_id'];



            // Update Here

            if ($postdata['target_date'] != '0000-00-00 00:00:00') {

                $data['target_date'] = $postdata['target_date'];

            }



            if ($postdata['completed_date'] != '0000-00-00 00:00:00') {

                $data['completed_date'] = $postdata['completed_date'];

            }



            if ($postdata['status'] != 0) {

                $data['status'] = (int)$postdata['status'];

            }

			

			if($postdata['kwh_savings'] != ''){

				$data['kwh_savings'] = $postdata['kwh_savings'];

			}

			

			if($postdata['cost_savings'] != ''){

				$data['cost_savings'] = $postdata['cost_savings'];

			}

            // pr($data);

            // pre($postdata);

            $this->db->where('site_id', $postdata['site_id']);

            $this->db->where('project_to_do_id', $postdata['todo_id']);

            return $this->db->update($this->_tbl_action_plan, $data);

            // pre($this->db->last_query());

        } else {

            // Add Here

            $data['user_id'] = $postdata['user_id'];

            $data['site_id'] = $postdata['site_id'];

            $data['project_to_do_id'] = $postdata['todo_id'];

            $data['target_date'] = '0000-00-00 00:00:00';

            $data['completed_date'] = '0000-00-00 00:00:00';

            $data['status'] = 0;

            return $this->db->insert($this->_tbl_action_plan, $data);

        }

    }



    function get_actionplans_todos($actiondata) {

        $this->db->select('t.*,a.status as astatus,a.target_date,a.completed_date,a.kwh_savings,a.cost_savings');

        $this->db->from($this->_tbl_projects_todos . ' as t');

        $this->db->join($this->_tbl_action_plan . ' as a', 'a.project_to_do_id = t.id');

        $this->db->where('t.project_id', $actiondata['project_id']);

        $this->db->where('a.site_id', $actiondata['site_id']);

        //$this->db->where('a.user_id', $actiondata['user_id']);

        $this->db->where('t.status != ', '-1');

        $result = $this->db->get();

        // pre($result);

        return $result->result_array();

    }

    

    function get_actionplans_todos_bysite($actiondata) {

        $this->db->select('t.*,a.status as astatus,a.target_date,a.completed_date,a.kwh_savings,a.cost_savings,s.local_currency');

        $this->db->from($this->_tbl_projects_todos . ' as t');

        $this->db->join($this->_tbl_action_plan . ' as a', 'a.project_to_do_id = t.id');

        $this->db->join($this->_tbl_sites . ' as s', 's.id = a.site_id');

        $this->db->where('t.project_id', $actiondata['project_id']);

        $this->db->where('a.site_id', $actiondata['site_id']);

        //$this->db->where('a.user_id', $actiondata['user_id']);

        $this->db->where('t.status != ', '-1');

        $this->db->group_by('a.project_to_do_id');

        $result = $this->db->get();

        return $result->result_array();

    }



    function getTodoComments($data = array()) {

        $this->db->select('c.*,u.firstname,u.lastname,u.username');

        $this->db->join($this->_tbl_projects_todos . ' as pd', 'pd.id = c.project_todo_id');

        $this->db->join($this->_tbl_users . ' as u', 'u.id = c.user_id');

        $this->db->from($this->_tbl_projects_todo_comments . ' as c');

        //$this->db->where('c.user_id', $data['user_id']);

        $this->db->where('c.site_id', $data['site_id']);

        $this->db->where('c.status !=', -1);

        $this->db->where('c.project_todo_id', $data['todo_id']);



        $result = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    function getTodoFiles($data = array()) {

        $this->db->select('f.*,u.firstname,u.lastname,u.username');

        $this->db->join($this->_tbl_projects_todos . ' as pd', 'pd.id = f.project_todo_id');

        $this->db->join($this->_tbl_users . ' as u', 'u.id = f.user_id');

        $this->db->from($this->_tbl_projects_todo_files . ' as f');

        //$this->db->where('f.user_id', $data['user_id']);

        $this->db->where('f.site_id', $data['site_id']);

        $this->db->where('f.status !=', -1);

        $this->db->where('f.project_todo_id', $data['todo_id']);



        $result = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    function getTodoComment($id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_projects_todo_comments);

        $this->db->where('id =', $id);

        $result = $this->db->get();

        return $result->row_array();

    }



    function save_todo_comment($postdata = array()) {

        $data = array();

        $data['comments'] = $postdata['comments'];

        $data['project_todo_id'] = $postdata['todo_id'];

        $data['hotel_id'] = $postdata['hotel_id'];

        $data['site_id'] = $postdata['site_id'];

        $data['status'] = $postdata['status'];



        $comment = $this->getTodoComment($postdata['comment_id']);

        if (!empty($comment)) {

            $data['modify_by'] = $postdata['user_id'];

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->where('id', $postdata['comment_id']);

            $this->db->update($this->_tbl_projects_todo_comments, $data);

            $id = $data['id'];

        } else {

            $data['user_id'] = $postdata['user_id'];

            $data['modify_by'] = $postdata['user_id'];

            $data['created_by'] = $postdata['user_id'];

            $this->db->set('created_on', 'NOW()', FALSE);

            $this->db->set('modify_on', 'NOW()', FALSE);

            $this->db->insert($this->_tbl_projects_todo_comments, $data);

            $id = $this->db->insert_id();

        }



        if ($this->db->affected_rows() > 0) {

            return true;

        } else {

            return false;

        }

    }



    function delete_todo_comment($postdata = array()) {

        //$this->db->where('site_id', $postdata['site_id']);

        $this->db->where('id', $postdata['comment_id']);

        $this->db->where('project_todo_id', $postdata['todo_id']);





        // code added by NP

        // -------

        //$this->db->set('status', -1);

        //$this->db->set('deleted_by', $this->user_id, FALSE);

        //$this->db->set('deleted_on', 'NOW()', FALSE);

        //$this->db->update($this->_tbl_projects_todo_comments);

        //----------

        // code added by HG

        $this->db->delete($this->_tbl_projects_todo_comments);

        if ($this->db->affected_rows() > 0) {

            return true;

        } else {

            return false;

        }

    }



    function delete_todo_comment_file($postdata = array()) {

        //$this->db->where('site_id', $postdata['site_id']);

        $this->db->where('id', $postdata['comment_id']);

        $this->db->where('project_todo_id', $postdata['todo_id']);

        $this->db->where('file', $postdata['file']);

        $this->db->delete($this->_tbl_projects_todo_files);

        if ($this->db->affected_rows() > 0) {

            return true;

        } else {

            return false;

        }

    }



    function save_todo_file($postdata = array()) {

        $data = array();

        $data['file'] = $postdata['file'];

        $data['project_todo_id'] = $postdata['todo_id'];

        $data['hotel_id'] = $postdata['hotel_id'];

        $data['site_id'] = $postdata['site_id'];

        $data['status'] = $postdata['status'];



        $data['user_id'] = $postdata['user_id'];

        $data['modify_by'] = $postdata['user_id'];

        $data['created_by'] = $postdata['user_id'];

        $this->db->set('created_on', 'NOW()', FALSE);

        $this->db->set('modify_on', 'NOW()', FALSE);

        $this->db->insert($this->_tbl_projects_todo_files, $data);

        $id = $this->db->insert_id();



        if ($this->db->affected_rows() > 0) {

            return true;

        } else {

            return false;

        }

    }



    function setActionplanExpiry() {

        $now_date = date('Y-m-d');



        $this->db->set('status', 4);

        $this->db->where('target_date <', $now_date);

        $this->db->where('target_date !=', '0000-00-00 00:00:00');

        $this->db->update($this->_tbl_action_plan);

    }



    function getProjectListings($site_id = 0, $role_id = 0, $category_id = 0) {

        if (isset($this->projects_search_term) && $this->projects_search_term != "") {

            $this->db->like("LOWER(p.project_name)", strtolower($this->projects_search_term));

        }

        /* if (isset($this->projects_sort_by) && $this->projects_sort_by != "" && $this->projects_sort_order != "") {

          $this->db->order_by('p.' . $this->projects_sort_by, $this->projects_sort_order);

          } */

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {

            $this->db->limit($this->record_per_page, $this->offset);

        }



        if (!empty($this->filter_category_id)) {

            $this->db->where('p.project_category_id', $this->filter_category_id);

        }



        if (!empty($this->filter_region_id)) {

            $this->db->where('r.id', $this->filter_region_id);

        }



        if (!empty($this->filter_color_id)) {

            $this->db->where('pdi.todo_color', $this->filter_color_id);

        }



        if ($this->filter_admin_site_id) {

            if ($this->filter_site_id != "" && $this->filter_site_id != "0" && $role_id != 1 && $role_id != 2) {

                $this->db->where('p.site_id', $this->filter_site_id);

            }

        } else {

            if ($role_id != 1 && $role_id != 2) {

                $this->db->where('p.site_id', $site_id);

            } else {

                $this->db->where('p.site_id', 0);

            }

        }



        if ($this->filter_site_id != "" && $this->filter_site_id != "0" && ($role_id == 1 || $role_id == 2)) {

            $this->db->select('p.*,pc.name,h.hotel_name,s.site_location_name, pdi.todo_color as ema_todo_color, pdi.todo_name as ema_todo_name, pdi.id as pdi_id');

        } else if ($role_id == 1 || $role_id == 2) {

            $this->db->select('p.*,pc.name,h.hotel_name,s.site_location_name, pdi.todo_color as ema_todo_color, pdi.todo_name as ema_todo_name, pdi.id as pdi_id');

        } else {

            $this->db->select('p.*,pc.name,h.hotel_name,s.site_location_name');

        }

        $this->db->from($this->_tbl_projects . ' as p');

        $this->db->join($this->_tbl_projects_categories . ' as pc', 'pc.id = p.project_category_id');

        $this->db->join($this->_tbl_hotels . ' as h', 'h.id = p.hotel_id');

        $this->db->join($this->_tbl_sites . ' as s', 's.id = p.site_id', 'LEFT');

        $this->db->join($this->_tbl_regions . ' as r', 'r.id = s.region_id', 'LEFT');



        //if($this->filter_site_id != "" && $this->filter_site_id != "0" && ($role_id == 1 || $role_id == 2)) {

        if ($role_id == 1 || $role_id == 2) {

            $this->db->join($this->_tbl_projects_todos . ' as pdi', 'pdi.project_id = p.id', 'LEFT');

            //$this->db->join($this->_tbl_action_plan.' as ap', 'ap.project_to_do_id = pdi.id','LEFT');

            //$this->db->join($this->_tbl_sites.' as aps', 'aps.id = ap.site_id','LEFT');

            //$this->db->where("ap.site_id IS NOT NULL");



            $this->db->where('pdi.status = 1');

        }



        if ((int) $category_id > 0) {

            $this->db->where('p.project_category_id', $category_id);

        }



        $this->db->where('p.status !=', -1); // check project status should not be deleted

        //$this->db->where('p.status !=', '0'); // check project status should not be inactive

        $this->db->where("(s.status IS NULL OR s.status = '1') "); // check site status should not be deleted

        //$this->db->where('s.status !=', '0'); // check site status should not be inactive



        if ($role_id == 1 || $role_id == 2) {

            $this->db->order_by("pc.name", "ASC");

            $this->db->order_by("p.project_name", "ASC");

            $this->db->order_by("pdi.todo_name", "ASC");

        }



        $result = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($result));

        } else {

            return $this->db->custom_result($result);

        }

    }



    function getAllActionPlansBySiteId($site_id = 0) {

        $this->db->select('*');

        $this->db->from($this->_tbl_action_plan);

        $this->db->where('site_id', $site_id);

        

        $result = $this->db->get();

        return $result->result_array();

    }



}

