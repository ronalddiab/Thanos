<?php

/**
 *  URL Model
 *
 *  To perform queries related to URL management.
 *
 * @package CIDemoApplication
 * @subpackage URLs
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Urls_model extends Base_Model
{

    protected $_tbl_url = TBL_URL_MANAGEMENT;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $search = '';
    public $module = '';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Function get_url_listing to fetch all records of url
     */
    function get_url_listing()
    {
//        if($this->search_term != "")
//        {
//            $this->db->like("LOWER(u.slug_url)", strtolower($this->search_term));
//        }


        if($this->search_term != "" && $this->search == "slug_url")
        {
            $this->db->like("LOWER(u.slug_url)", strtolower($this->search_term));
        }
        if($this->module != "" && $this->search == "module_name")
        {
            $this->db->like("LOWER(u.module_name)", strtolower($this->module));
        }

        if($this->sort_by != "" && $this->sort_order != "")
        {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if(isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
           $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('u.*');
        $this->db->from($this->_tbl_url . ' AS u');
        $this->db->where('u.status !=', -1);
        $query = $this->db->get();

        //echo $this->db->last_query();

        if(isset($this->_record_count) && $this->_record_count == true)
        {
            return count($this->db->custom_result($query));
        }
        else
        {
            return $this->db->custom_result($query);
        }

        return $result;
    }

    /**
     * Function get_url_listing to fetch all records of url by id
     * @param integer $id
     */
    function get_url_by_id($id = 0)
    {
        //Type Casting
        $id = intval($id);

        $this->db->select('u.*');
        $this->db->from($this->_tbl_url . ' AS u');
        $this->db->where('u.id', $id);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function save_url to add/update URL
     */
    public function save_url($data)
    {
        if ($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update($this->_tbl_url, $data);
            $id = $data['id'];
        }
        else
        {
            $this->db->insert($this->_tbl_url, $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }

    /**
     * Function get_records to fetch all records of given table
     * @param string $table
     */
    function get_records($table = NULL)
    {

        $this->db->select('u.id,u.title');
        $this->db->from($table . ' AS u');
        $this->db->where('u.status', 1);
        $this->db->order_by('u.title', 'asc');
        $query = $this->db->get();

        return $this->db->custom_result($query);
    }

    /**
     * Function delete_url to delete URL
     * @param integer $id
     */
    public function delete_url($id = 0)
    {
        //Type Casting
        $id = intval($id);

        $this->db->where('id', $id);
        return $this->db->delete($this->_tbl_url);
    }


    /**
     * Function delete_url_by_slug to delete URL
     * @param integer $slug_url
     */
    public function delete_url_by_slug($slug_url)
    {
        //Type Casting
        $slug_url = trim(strip_tags($slug_url));

        $this->db->where('slug_url', $slug_url);
        $this->db->delete($this->_tbl_url);

    }

    /**
     * Function get_url_management_id_by_slug to get url detail
     * @param string $slug_url
     */
    public function get_url_management_id_by_slug($slug_url="") {
        $this->db->select('um.*')
                ->from("url_management as um")
                ->where('um.slug_url', $slug_url."");
        $result = $this->db->get();

        return $this->db->custom_result($result);
    }

    /**
     * Function check_unique_slug to check unique slug
     * @param string $slug_url
     * @param string $id
     */
    public function check_unique_slug($slug_url, $id ="") {
        //Type Casting
        $slug_url = trim(strip_tags($slug_url));
        $id = intval($id);

        $this->db->select('um.*');
        $this->db->from("url_management as um");
        $this->db->where('um.slug_url', $slug_url."");
        if($id != '0' && $id != '')
        {
            $this->db->where('um.id !=', $id);
        }
        $this->db->where('um.status !=', -1);

        $result = $this->db->get();
        return $this->db->custom_result($result);
    }

    /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array())
    {
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_url);

        return true;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records()
    {
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_url);

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
        $this->db->update($this->_tbl_url);

        return true;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records()
    {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_url);

        return true;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id
     */
    public function delete_records($id = array())
    {
        $this->db->where_in('id', $id);
        return $this->db->delete($this->_tbl_url);
    }

}

