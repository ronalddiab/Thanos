<?php

/**
 *  Forum Model (actual table -  forum_post)
 *
 *  To perform queries related to  Forum management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 *
 * @author AVSH
 */
class Forum_post_model extends Base_Model {

    protected $_tbl_forum_post = TBL_FORUM_POST;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $offset = "";
    public $keywords = "";
    public $postback_url = "forum_listing";

    /**
     * Function get_forum_listing for get forums
     *
     */
    public function get_forum_listing($category) {

        if ((isset($this->search_term) && $this->search_term != "")) {
            $this->db->like("LOWER(forum_post_title)", strtolower($this->search_term));
        }
        if (isset($this->search_term) && isset($this->search_term) && $this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        if (isset($this->id)) {
            $this->db->where('id =', $this->id);
        }
        if (isset($this->front)) {
            $this->db->where('status =', 1);
        } else {
            $this->db->where('status !=', -1);
        }

        $this->db->select('*');
        $this->db->from($this->_tbl_forum_post);
        $this->db->where('status !=', -1);
        if ($category != "") {
            $this->db->where('category_id =', intval($category));
        }
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();

        // echo $this->db->last_query(); exit;

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    function getMembers($name) {

        $userIDs = array();

        $this->db->select('*');
        $this->db->from(TBL_USERS . ' as U');
        $this->db->where('U.status', 1);
        $this->db->like('U.firstname', $name);
        $this->db->or_like('U.lastname', $name);

        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $userIDs[] = $row->id;
        }

        return $userIDs;
    }

//    function getTags($name) {
//
//        $tagIDs = array();
//
//        $this->db->select('*');
//        $this->db->from(TBL_TAGS . ' as U');
//        $this->db->where('U.status', 1);
//        $this->db->like('U.tag_value', $name);
//
//        $query = $this->db->get();
//
//        foreach ($query->result() as $row) {
//            $tagIDs[] = $row->id;
//        }
//
//        return $tagIDs;
//    }
//
//    function getForums($ids) {
//
//        $ForumsIDs = array();
//
//        $this->db->select('*');
//        $this->db->from(TBL_FORUM_TAG . ' as U');
//        $this->db->where_in('U.tag_id', $ids);
//
//        $query = $this->db->get();
//        foreach ($query->result() as $row) {
//            $ForumsIDs[] = $row->forum_id;
//        }
//
//        return $ForumsIDs;
//    }
    // public function get_forum_listing_front($category, $language_id) {
    public function get_forum_listing_front() {

        /**
         * @Searching data ...
         */
        if (!empty($this->session->userdata[get_current_section($this, true)]['forum_keywords'])) {

            // echo "Here"; exit;
//            $getTags = array();
//            $getTags = $this->getTags($this->session->userdata[get_current_section($this, true)]['forum_keywords']);
//
//            if (!empty($getTags)) {
//                $getForums = array();
//                $getForums = $this->getForums($getTags);
//
//                if (!empty($getForums)) {
//                    $this->db->where_in("forum_post.id", $getForums);
//                }
//            }
//            $this->db->like("LOWER(forum_post.forum_post_title)", strtolower($this->session->userdata[get_current_section($this, true)]['forum_keywords']));
//            $this->db->or_like("LOWER(forum_post.forum_post_text)", strtolower($this->session->userdata[get_current_section($this, true)]['forum_keywords']));

            $forum_keywords = strtolower(mysql_real_escape_string($this->session->userdata[get_current_section($this, true)]['forum_keywords']));

            $where = "(T.tag_value like '%" . $forum_keywords . "%' "
                    . "OR "
                    . "LOWER(forum_post.forum_post_title) like '%" . $forum_keywords . "%' "
                    . "OR "
                    . "LOWER(forum_post.forum_post_text) like '%" . $forum_keywords . "%')";

            $this->db->where($where);
        }


        if (!empty($this->session->userdata[get_current_section($this, true)]['forum_date_from']) || !empty($this->session->userdata[get_current_section($this, true)]['forum_date_to'])) {

            $forum_date_from = $this->session->userdata[get_current_section($this, true)]['forum_date_from'];
            $forum_date_to = $this->session->userdata[get_current_section($this, true)]['forum_date_to'];

            if (!empty($forum_date_from) && !empty($forum_date_to)) {
                $this->db->where("DATE(forum_post.created_on) BETWEEN '$forum_date_from' AND '$forum_date_to' ");
            }

            if (!empty($forum_date_from) && empty($forum_date_to)) {
                $this->db->where("DATE(forum_post.created_on) >= '$forum_date_from'");
            }

            if (empty($forum_date_from) && !empty($forum_date_to)) {
                $this->db->where("DATE(forum_post.created_on) <= '$forum_date_to' ");
            }
        }

        if (!empty($this->session->userdata[get_current_section($this, true)]['forum_industry_id'])) {
            $forum_industry_id = $this->session->userdata[get_current_section($this, true)]['forum_industry_id'];
            $this->db->where("forum_post.industry_id", $forum_industry_id);
        }

        if (!empty($this->session->userdata[get_current_section($this, true)]['forum_sector_id'])) {
            $forum_sector_id = $this->session->userdata[get_current_section($this, true)]['forum_sector_id'];
            $this->db->where("forum_post.sector_id", $forum_sector_id);
        }

        if (!empty($this->session->userdata[get_current_section($this, true)]['forum_member'])) {
            $forum_member = mysql_real_escape_string($this->session->userdata[get_current_section($this, true)]['forum_member']);

            // $this->db->where("forum_post.created_by", $forum_member);
            $this->db->like("U.firstname", $forum_member);
            $this->db->or_like("U.lastname", $forum_member);


//            $getMembers = array();
//            $getMembers = $this->getMembers($forum_member);
//
//            // echo "<pre>"; print_r(NetworkKing); exit;
//
//            if (!empty($getMembers)) {
//                $this->db->where_in("forum_post.created_by", $getMembers);
//            } else {
//                $this->db->where_in("forum_post.created_by", array(0));
//            }
        }

        /**
         * @Sorting data ...
         */
        if ($this->sort_by != "" && $this->sort_order != "") {
            $this->db->order_by($this->sort_by, $this->sort_order);
        } else {
            $this->db->order_by('forum_post.category_id', "ASC");
            $this->db->order_by('forum_post.is_sticky', "DESC");
        }

        /**
         * @Paging data ...
         */
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }


//        if (isset($this->id)) {
//            $this->db->where('forum_post.id =', $this->id);
//        }

        $this->db->select('forum_post.*, c.title, (SELECT count(*) from ' . $this->_tbl_forum_post . ' as P WHERE P.category_id = forum_post.category_id AND P.status = 1) as count, U.firstname, U.lastname');
        $this->db->from($this->_tbl_forum_post . ' as forum_post');
        $this->db->join(TBL_CATEGORIES . ' as c', 'forum_post.category_id = c.category_id', 'left');
        $this->db->join(TBL_INDUSTRIES . ' as i', 'forum_post.industry_id = i.industry_id', 'left');
        $this->db->join(TBL_SECTORS . ' as s', 'forum_post.sector_id = s.sector_id', 'left');
        $this->db->join(TBL_FORUM_TAG . ' as FT', 'forum_post.id = FT.forum_id', 'left');
        $this->db->join(TBL_TAGS . ' as T', 'T.tag_id = FT.tag_id', 'left');
        $this->db->join(TBL_USERS . ' as U', 'U.id = forum_post.created_by', 'left');
        //$this->db->where('forum_post.lang_id', $language_id);
        $this->db->where('forum_post.status', 1);
        $this->db->group_by('forum_post.id');
        // $this->db->order_by('forum_post.created_on', 'desc');
        $query = $this->db->get();

        // echo $this->db->last_query();exit;

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    public function get_today_thread() {

        /**
         * @Paging data ...
         */
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('forum_post.*');
        $this->db->from($this->_tbl_forum_post . ' as forum_post');
        //$this->db->where('forum_post.lang_id', $language_id);
        $this->db->where('forum_post.status', 1);
        $this->db->where('DATE(forum_post.created_on)', date('Y-m-d'));
        $query = $this->db->get();

        // echo $this->db->last_query(); exit;

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    function getForumTags($id) { // $id is forum id .....
        $tagarr = array();

        $this->db->select('*');
        $this->db->from(TBL_FORUM_TAG);
        $this->db->where('forum_id', $id);
        $query = $this->db->get();

        foreach ($query->result() as $value) {
            $tagarr[] = $value->tag_id;
        }

        return $tagarr;
    }

    /**
     * Function forum_post_by_id for get forums by id
     *
     */
    function forum_tags($id) {

        $sql = "SELECT GROUP_CONCAT(`t`.`tag_value` SEPARATOR ', ') as tag_value FROM (" . TBL_FORUM_TAG . " as ft) LEFT JOIN " . TBL_TAGS . " as t ON `t`.`tag_id` = `ft`.`tag_id` WHERE `ft`.`forum_id` = " . intval($id);

        $query = $this->db->query($sql);
        $result = $query->row_array();

        return $result['tag_value'];
    }

    public function forum_post_by_id($id) {

        $this->db->select('fp.*, u.firstname, u.lastname, up.*, s.sector_name, i.industry_name ');
        $this->db->from($this->_tbl_forum_post . " as fp");
        $this->db->where('fp.status !=', -1);
        $this->db->join(TBL_USERS . ' as u', 'u.id = fp.created_by', 'left');
        $this->db->join(TBL_USER_PROFILE . ' as up', 'up.user_id = fp.created_by', 'left');
        $this->db->join(TBL_INDUSTRIES . ' as i', 'i.industry_id = fp.industry_id', 'left');
        $this->db->join(TBL_SECTORS . ' as s', 's.sector_id = fp.sector_id', 'left');

        /*if ($language_id != "") {
            $this->db->where('fp.lang_id', $language_id);
        }*/

        if ($id != "") {
            $this->db->where('fp.id =', intval($id));
        }
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if (!empty($result)) {
            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * Function total_forum_by_category for get count of total forum by category
     *
     */
    public function total_forum_by_category($id) {
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_post);
        $this->db->where('status !=', -1);
        $this->db->where('category_id =', $id);
        $result = $this->db->get();
        return count($result->result_array());
    }

    /**
     * Function updated_date for modify date
     *
     */
    public function updated_date($data) {
        $this->db->where('id', $data['id']);
        $this->db->set('modified_on', 'NOW()', FALSE);
        $this->db->update($this->_tbl_forum_post, $data);
    }

    /**
     * Function forum_adder for add forum
     *
     */
    public function forum_adder($data) {
        if ($data['id'] != 0 && $data['id'] != "") {
            $this->db->where('id', $data['id']);
            $this->db->set('modified_on', 'NOW()', FALSE);
            $this->db->update($this->_tbl_forum_post, $data);
            $id = $data['id'];
        } else {
            $this->db->set('created_on', 'NOW()', FALSE);
            $this->db->insert($this->_tbl_forum_post, $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }

    public function save_tags($tag_id, $id) {

        $this->db->where('forum_id', $id);
        $this->db->delete(TBL_FORUM_TAG);

        if (!empty($tag_id)) {
            foreach ($tag_id as $k => $v) {
                $this->db->insert(TBL_FORUM_TAG, array('forum_id' => $id, 'tag_id' => $v));
            }
        }
    }

    /**
     * Function delete_forum for delete forum
     *
     */
    public function delete_forum($id) {
        //Type Casting
        $id = intval($id);

        $this->db->where('id', $id);
        $this->db->set('status', '-1');
        $this->db->set('deleted_by', $this->session->userdata[$this->theme->get('section_name')]['user_id']);
        $this->db->set('deleted_on', 'NOW()', FALSE);
        return $this->db->update($this->_tbl_forum_post);
    }

    /**
     * Function check_unique_slug for check unique slug
     *
     */
    public function check_unique_slug($slug_url, $post_id = 0) {
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_post);
        if (isset($post_id) && $post_id != '' && $post_id != 0) {
            $this->db->where('id != ', $post_id);
        }
        $this->db->where('slug_url', $slug_url);
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }

    /**
     * Function get_id_from_slug for get id from slug
     *
     */
    public function get_id_from_slug($slug_url) {
        $this->db->select('id');
        $this->db->from($this->_tbl_forum_post);
        $this->db->where('slug_url', $slug_url);
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if (!empty($result)) {
            return $result[0];
        } else {
            return false;
        }
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array()) {
        $this->db->set('status', 2);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_forum_post);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records() {
        $this->db->set('status', 2);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_forum_post);

        return true;
    }

    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array()) {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_forum_post);

        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records() {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_forum_post);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array()) {
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_forum_post);
    }

    function get_moderator_listing() {
        if (isset($this->moderator_search_term) && $this->moderator_search_term != "") {
            $this->db->like("LOWER(u.firstname)", strtolower($this->moderator_search_term));
        }
        if (isset($this->moderator_sort_by) && $this->moderator_sort_by != "" && $this->moderator_sort_order != "") {
            $this->db->order_by('u.' . $this->moderator_sort_by, $this->moderator_sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('u.*');
        $this->db->from(TBL_USERS . ' AS u');
        $this->db->where('u.status !=', -1);
        $this->db->where('u.role_id', 5);
        $this->db->order_by('u.id', 'DESC');

        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {
            return count($this->db->custom_result($query));
        } else {
            return $this->db->custom_result($query);
        }
    }

    function insert_moderator($data) {

        /**
         * @Insert moderator
         */
        $this->db->insert(TBL_USERS, $data);
        $mod_id = $this->db->insert_id();

        /**
         * @Insert moderator permissions
         */
        $ins_per = array();
        $ins_per = explode(',', MODERATOR_DEFAULT_PERMISSION);

        if (!empty($ins_per)) {
            foreach ($ins_per as $val) {
                $this->db->insert(TBL_USER_PERMISSION, array('user_id' => $mod_id, 'permission_id' => $val));
            }
        }
    }

    function update_moderator($data) {

        $id = intval($data['id']);

        $this->db->where('id', $id);
        $this->db->update(TBL_USERS, $data);
    }

    function get_moderator($id) {

        $id = intval($id);

        $this->db->select('u.*');
        $this->db->from(TBL_USERS . ' AS u');
        $this->db->where('u.id', $id);
        $query = $this->db->get();

        $result = $query->row_array();
        return $result;
        //echo "<pre>"; print_r($result); exit;
    }

    function getPermList() {

        /**
         * @Default permissions prevents to show
         */
        $ins_per = array();
        $ins_per = explode(',', MODERATOR_DEFAULT_PERMISSION);

        $this->db->select('P.*');
        $this->db->from(TBL_PERMISSIONS . ' AS P');
        $this->db->where('P.status', 1);
        $this->db->where('P.parent_id', 320);
        $this->db->where_not_in('id', $ins_per);
        $query = $this->db->get();

//        if (isset($this->_record_count) && $this->_record_count == true) {
//            return count($this->db->custom_result($query));
//        } else {
//            return $this->db->custom_result($query);
//        }

        return $this->db->custom_result($query);
    }

    function getSetPerm($id) {


        /**
         * @Default permissions prevents to show
         */
        $ins_per = array();
        $ins_per = explode(',', MODERATOR_DEFAULT_PERMISSION);

        $permit_arr = array();

        $this->db->select('P.*');
        $this->db->from(TBL_USER_PERMISSION . ' AS P');
        $this->db->where('P.user_id', $id);
        $this->db->where_not_in('P.permission_id', $ins_per);
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $permit_arr[] = $row->permission_id;
        }

        return $permit_arr;
    }

    function save_permission($data) {

        $id = intval($data['id']);

        /**
         * @Default permissions prevents to delete
         */
        $ins_per = array();
        $ins_per = explode(',', MODERATOR_DEFAULT_PERMISSION);

        $this->db->where('user_id', $id);
        $this->db->where_not_in('permission_id', $ins_per);
        $this->db->delete(TBL_USER_PERMISSION);

        if (!empty($data['check_box'])) {

            foreach ($data['check_box'] as $v) {
                $this->db->insert(TBL_USER_PERMISSION, array('user_id' => $id, 'permission_id' => $v));
            }
        }
    }

    public function get_forum_listing_for_member($memberId = 0) {
        if ($memberId != 0) {
            $this->db->where('created_by =', $memberId);
            $this->db->select('*');
            $this->db->from($this->_tbl_forum_post);
            $this->db->where('status', 1);

            if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
                $this->db->limit($this->record_per_page, $this->offset);
            }

            $query = $this->db->get();
            if (isset($this->_record_count) && $this->_record_count == true) {
                return count($this->db->custom_result($query));
            } else {
                return $this->db->custom_result($query);
            }
        }
    }

    public function get_conttribution_for_member($memberId = 0) {

        if ($memberId != 0) {

            $this->db->select('FT.*, forum_post.*');
            // $this->db->from('forum_topics as FT');
            $this->db->from(TBL_FORUM_TOPICS . ' as FT');
            $this->db->join($this->_tbl_forum_post . ' as forum_post', 'forum_post.id = FT.post_id', 'left');
            $this->db->where('FT.created_by', $memberId);
            $this->db->where('FT.status', 1);
            $this->db->group_by('FT.post_id');

            if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
                $this->db->limit($this->record_per_page, $this->offset);
            }


            $query = $this->db->get();

            if (isset($this->_record_count) && $this->_record_count == true) {
                return count($this->db->custom_result($query));
            } else {
                return $this->db->custom_result($query);
            }
        }
    }

    function update_slug($id) {

        /**
         * @Get Title
         */
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_post);
        $this->db->where('id', $id);
        $query = $this->db->get();

        $result = $query->row_array();

        /**
         * @Operations on slug
         */
        $title = strtolower($result['forum_post_title']);
        $title = clean($title);
        $title.="-". $result['id'];
        $title = preg_replace('/[\s-]+/', '-', $title);

//        $title = str_replace(" ", "-", $title);
//        $title = str_replace("'", "", $title);


        /**
         * @Save slug
         */
        $this->db->where('id', $result['id']);
        $this->db->update($this->_tbl_forum_post, array('slug_url' => $title));
    }

}
