<?php
/**
 *  Forum Model (actual table -  forum_topic)
 *
 *  To perform queries related to  Forum management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 * @author AVSH
 */

class Forum_topics_model extends Base_Model
{
    protected $_tbl_forum_topics = TBL_FORUM_TOPICS;
    public $search_term ="";
    public $sort_by ="";
    public $sort_order ="";
    public $offset ="";

    /**
     * Function get_topics_from_post for get topics(replies) from post
     *
     */
    public function get_topics_from_post($id)
    {

        if((isset($this->search_term) && $this->search_term != ""))
        {
            $this->db->like("LOWER(topic_title)", strtolower($this->search_term));
        }
        if(isset($this->sort_by) && isset($this->sort_order) && $this->sort_by != "" && $this->sort_order != "")
        {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('ft.*, u.firstname, u.lastname, MAX(ft.created_on) AS lastupdate, up.* ');
        $this->db->from($this->_tbl_forum_topics." as ft");
        //$this->db->where('ft.lang_id',$language_id);
        $this->db->where('ft.status !=', -1);
        $this->db->group_by('ft.id');
        $this->db->join(TBL_USERS . ' as u', 'u.id = ft.created_by', 'left');
        $this->db->join(TBL_USER_PROFILE . ' as up', 'up.user_id = ft.created_by', 'left');


        if(isset($id))
        {
            $this->db->where('ft.post_id =',$id);
        }
        if(isset($this->front))
        {
            $this->db->where('ft.status =', 1);
        }
        else
        {
            $this->db->where('ft.status !=', -1);
        }
        $query = $this->db->get();
        if(isset($this->_record_count) && $this->_record_count == true)
        {
            return count($this->db->custom_result($query));
        }
        else
        {
            return $this->db->custom_result($query);
        }
    }
    /**
     * Function get_record_by_id for get topics(replies) from topic_id
     *
     */
    public function get_record_by_id($id)
    {

        $this->db->select('*');
        $this->db->from($this->_tbl_forum_topics);
        $this->db->where('status !=', -1);
        //$this->db->where('lang_id',$language_id);
        $this->db->group_by('id');

        if(isset($id))
        {
            $this->db->where('id =',$id);
        }
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if(!empty($result))
        {
            return $result[0];
        }
        else
        {
            return $result;
        }
    }
    /**
     * Function last_update_datetime for get last update date & time
     *
     */
    public function last_update_datetime($id)
    {

        $this->db->select('MAX(created_on) AS lastupdate');
        $this->db->from($this->_tbl_forum_topics);
        $this->db->where('status !=', -1);
        $this->db->group_by('post_id');
        if(isset($id))
        {
            $this->db->where('post_id =',$id);
        }
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if(!empty($result))
        {
            return $result[0];
        }
        else
        {
            return $result;
        }

    }
    /**
     * Function forum_post_by_id for get all topic data with user data
     *
     */
    public function forum_post_by_id($id)
    {

        $this->db->select('ft.*, u.firstname, u.lastname, up.* ');
        $this->db->from($this->_tbl_forum_topics." as ft");
        $this->db->where('ft.status !=', -1);
        $this->db->join(TBL_USERS . ' as u', 'u.id = ft.created_by', 'left');
        $this->db->join(TBL_USER_PROFILE . ' as up', 'up.user_id = ft.created_by', 'left');
        //$this->db->where('ft.lang_id',$language_id);
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_topics);
        $this->db->where('status !=', -1);
        if($id!="")
        {
            $this->db->where('ft.post_id =', intval($id));
        }
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        return $result;
    }
    /**
     * Function record_count for get Number of topics on post
     */
    public function record_count()
    {
        if(isset($this->search_term) && $this->search_term)
        {
            $this->db->like("LOWER(forum_post_title)", strtolower($this->search_term));
        }
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_topics);
        $this->db->where('status !=', -1);
        //$this->db->where('lang_id',$language_id);
        if(isset($this->id))
        {
            $this->db->where('post_id =', $this->id);
        }
        $result = $this->db->get();
        return count($result->result_array());
    }
    /**
     * Function add_reply for add reply(topic) to post
     *
     */
    public function add_reply($data)
    {
          if($data['id'] != 0 && $data['id'] != "")
            {
                $this->db->where('id', $data['id']);
                $this->db->set('modified_on', 'NOW()', FALSE);
                $this->db->update($this->_tbl_forum_topics, $data);
                $id = $data['id'];
                return $id;
            }
            else
            {
                $data['status']=1;
                $this->db->set('created_on', 'NOW()', FALSE);
                $this->db->insert($this->_tbl_forum_topics, $data);
            }
    }
    /**
     * Function delete_topic for delete reply(topic)
     *
     */
    public function delete_topic()
    {

        if(isset($this->id) && $this->id)
        {
              $id = intval($this->id);
              $this->db->where('id', $id);
              $this->db->set('status', '-1');
              $this->db->set('deleted_by', $this->session->userdata[$this->theme->get('section_name')]['user_id']);
              $this->db->set('deleted_on', 'NOW()', FALSE);
              return $this->db->update($this->_tbl_forum_topics);
        }
        else if(isset($this->post_id) && $this->post_id)
        {
              $id = intval($this->post_id);
              $this->db->where('post_id', $id);
              $this->db->set('status', '-1');
              $this->db->set('deleted_by', $this->session->userdata[$this->theme->get('section_name')]['user_id']);
              $this->db->set('deleted_on', 'NOW()', FALSE);
              return $this->db->update($this->_tbl_forum_topics);
        }
    }
    /**
     * Function inactive_all_records to inactive records
     * @param array $id
     */
    public function inactive_all_records()
    {
        $this->db->set('status', 2);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_forum_topics);
        return true;
    }
    /**
     * Function active_records to active records
     * @param array $id
     */
    public function active_records($id = array())
    {
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_forum_topics);
        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records()
    {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->where('id !=', 1);
        $this->db->update($this->_tbl_forum_topics);
        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array())
    {
        $this->db->where_in('id', $id);
        $this->db->set('status', '-1');
        return $this->db->update($this->_tbl_forum_topics);
    }
    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array())
    {
        $this->db->set('status', 2);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_forum_topics);

        return $id;
    }
    public function get_conttribution_for_member($memberId=0) {
         if ($memberId != 0) {
            $this->db->where('created_by =',$memberId);
            $this->db->select('*');
            $this->db->from($this->_tbl_forum_topics);
            $this->db->where('status', 1);
            $query = $this->db->get();
            if (isset($this->_record_count) && $this->_record_count == true) {
                return count($this->db->custom_result($query));
            } else {
                return $this->db->custom_result($query);
            }
        }
    }

}
