<?php
/**
 *  Forum Model (actual table -  forum_category)
 *
 *  To perform queries related to  Forum management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 * @author AVSH
 */

class Forum_category_model extends Base_Model
{
    protected $_tbl_forum_catagories = TBL_CATEGORIES;
    protected $_tbl_users = TBL_USERS;
    protected $_tbl_roles = TBL_ROLES;
    public $search_term ="";
    public $sort_by ="";
    public $sort_order ="";
    public $offset ="";
    /**
     * Function get_category_listing for get categories of forum
     *
     */
    public function get_category_listing()
    {

        if(isset($this->search_term) && $this->search_term != "")
        {
            $this->db->like("LOWER(title)", strtolower($this->search_term));
        }
        if(isset($this->search_term) && isset($this->search_term) && $this->sort_by != "" && $this->sort_order != "")
        {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
         if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        if(isset($this->id))
        {
            $this->db->where('id =',$this->id);
        }
        $this->db->where('module_id =',4);
        $this->db->select('*');
        $this->db->from($this->_tbl_forum_catagories);
        if(isset($this->front))
        {
            $this->db->where('status =', 1);
        }
        else
        {
            $this->db->where('status !=', -1);
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
     * Function get_category_from_id for get categories from id
     *
     */
    public function get_category_from_id($category_id)
    {
        if($category_id != "")
        {
            $this->db->where('category_id =', $category_id);
        }
        //$this->db->where('lang_id',$language_id);
        $this->db->where('module_id =',4);
        $this->db->select('title');
        $this->db->from($this->_tbl_forum_catagories);
        $query = $this->db->get();

        //echo $this->db->last_query(); exit;

        $result = $this->db->custom_result($query);
        if(!empty($result))
        {
            return $result[0];
        }
        else
        {
            return false;
        }

    }
    /**
     * Function get_id_from_slug for get id from slug
     *
     */
    public function get_id_from_slug($slug_url)
    {
        $this->db->select('category_id');
        $this->db->from($this->_tbl_forum_catagories);
        $this->db->where('slug_url', $slug_url);
        $this->db->where('module_id', 4);
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if(!empty($result))
        {
            return $result[0];
        }
        else
        {
            return false;
        }
    }
    /**
     * Function category_name_getter for get category name from id
     *
     */
    public function category_name_getter($category_id,$language_id)
    {
        $this->db->select('title');
        $this->db->from($this->_tbl_forum_catagories);
        $this->db->where('category_id', $category_id);
        $this->db->where('module_id', 4);
        $this->db->where('lang_id', $language_id);
        $this->db->where('status !=', -1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        if(!empty($result))
        {
            return $result[0];
        }
        else
        {
            return false;
        }
    }

}
