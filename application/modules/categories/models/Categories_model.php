<?php

/**
 *  Categories Model
 *
 *  To perform queries related to category management.
 *
 * @package CIDemoApplication
 * @subpackage Categories
 * @copyright	(c) 2013, TatvaSoft
 * @author HTDO
 */
class Categories_model extends Base_Model
{
    protected $_table = TBL_CATEGORIES;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";
    public $offset ="";
    public $parent_id = "";
    public $module_id = "";
    public $title="";
    public $slug_url="";
    public $description="";
    public $status="";
    public $category_array = array();
    public $module_array = array();
    public $first_option = "";

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Function get_category_detail_by_slug to fetch detail of records by slug
     */
    public function get_category_detail_by_slug($slug_url)
    {
        $slug_url = trim(strip_tags($slug_url));
        $this->db->select("c.*");
        $this->db->from($this->_table." as c");
        $this->db->where('c.slug_url', $slug_url);
        $this->db->where('c.status', 1);
        $this->db->where('c.lang_id', $this->{'category_model'}->language_id);
        $query = $this->db->get();

        if ($query->num_rows() != 0)
        {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function get_category_id_from_slug_url to fetch category id from slug
     */
    function get_category_id_from_slug_url($slug_url)
    {
        $slug_url = trim(strip_tags($slug_url));
        $this->db->select("c.*");
        $this->db->from($this->_table." as c");
        $this->db->where('c.slug_url', $slug_url);
        $this->db->where('c.status', 1);
        $query = $this->db->get();

        if ($query->num_rows() != 0)
        {
            return $this->db->custom_result($query);
        }
    }

    /**
     * Function insert_category to insert record
     */
    function insert_category($category_id, $lang_id)
    {
        $category_id = intval($category_id);
        $lang_id = intval($lang_id);

        $data_array = array();

        $data_array['category_id'] = $category_id;
        $data_array['lang_id'] = $lang_id;
        if(isset($this->parent_id) )
        {
            $data_array['parent_id'] = $this->parent_id;
        }
        if(isset($this->module_id) )
        {
            $data_array['module_id'] = $this->module_id;
        }
        if(isset($this->title) )
        {
            $data_array['title'] = $this->title;
        }
        if(isset($this->slug_url) )
        {
            $data_array['slug_url'] = $this->slug_url;
        }
        if(isset($this->description) )
        {
            $data_array['description'] = $this->description;
        }
        if(isset($this->status) )
        {
            $data_array['status'] = $this->status;
        }
        $data_array['created'] = GetCurrentDateTime();
        $data_array['created_by'] = $this->session->userdata[get_current_section($this,true)]['user_id'];


        $this->db->set($data_array);
        return $this->db->insert($this->_table);
        // return $this->db->_error_number(); // return the error occurred in last query
    }

    /**
     * Function update_category to update record
     */
    function update_category($category_id, $lang_id)
    {
        $category_id = intval($category_id);
        $lang_id = intval($lang_id);

        $data_array = array();

        $data_array['category_id'] = $category_id;
        $data_array['lang_id'] = $lang_id;
        if(isset($this->parent_id) )
        {
            $data_array['parent_id'] = $this->parent_id;
        }
        if(isset($this->module_id) )
        {
            $data_array['module_id'] = $this->module_id;
        }
        if(isset($this->title) )
        {
            $data_array['title'] = $this->title;
        }
        if(isset($this->slug_url) )
        {
            $data_array['slug_url'] = $this->slug_url;
        }
        if(isset($this->description) )
        {
            $data_array['description'] = $this->description;
        }
        if(isset($this->status) )
        {
            $data_array['status'] = $this->status;
        }
        $data_array['modified'] = GetCurrentDateTime();
        $data_array['modified_by'] = $this->session->userdata[get_current_section($this,true)]['user_id'];

        $this->db->where(array('category_id' => $category_id, 'lang_id' => $lang_id));
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_category_detail_by_id to get category detail
     */
    public function get_category_detail_by_id($category_id, $lang_id)
    {
        $category_id = intval($category_id);
        $lang_id = intval($lang_id);

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                ->where(array('c.category_id' => $category_id, 'c.lang_id' => $lang_id))
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }
    public function get_category_view_detail_by_id($category_id, $lang_id)
    {
        $category_id = intval($category_id);
        $lang_id = intval($lang_id);

        $this->db->select('c.*,cm.title as module_title')
                ->from($this->_table . ' as c')
                ->where(array('c.category_id' => $category_id, 'c.lang_id' => $lang_id))
                ->join(TBL_CATEGORY_MODULES . ' as cm', 'c.module_id = cm.id', 'left')
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_category_listing to get category listing
     */
    function get_category_listing()
    {
        if($this->search_term != "")
        {
            $this->db->like('LOWER(c.title)', strtolower($this->search_term), 'both');
        }
        if($this->sort_by != "" && $this->sort_order != "")
        {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }else{
            $this->db->order_by('c.id', "DESC");
        }



        if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
            $this->db->limit($this->record_per_page, $this->offset);
        }
        $this->db->select('c.*, l.*, cm.id, cm.title');
        $this->db->from($this->_table . ' as c');
        $this->db->join(TBL_LANGUAGES . ' as l', 'c.lang_id = l.id', 'left');
        $this->db->join(TBL_CATEGORY_MODULES . ' as cm', 'c.module_id = cm.id', 'left');

        $this->db->where('c.status !=', -1);

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
     * Function delete_category to delte category record
     */
    public function delete_category($id)
    {
        $id = intval($id);

        $data_array = array('status' => '-1');
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_related_lang_category to check category record exist or not
     */
    public function get_related_lang_category($category_id, $lang_id, $module_id)
    {
        $category_id = intval($category_id);
        $lang_id = intval($lang_id);
        $module_id = intval($module_id);

        $this->db->select('c.*')
                ->from($this->_table . ' as c')
                ->where(array('c.category_id' => $category_id, 'c.lang_id' => $lang_id, 'c.module_id' => $module_id))
                ->where('c.status !=', -1);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * Function get_last_category_id to get last category inserted id
     */
    function get_last_category_id()
    {
        $this->db->select_max('category_id')
                ->from($this->_table);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            $result = $query->row_array();
            return $result['category_id'];
        } else
        {
            return 0;
        }
    }

    /**
     * Function update_slug to update slug url
     */
    function update_slug($id, $slug_url)
    {
        $id = intval($id);
        $slug_url = trim(strip_tags($slug_url));
        $data_array = array(
            'slug_url' => $slug_url,
            'modified' => GetCurrentDateTime(),
            'modified_by' => $this->session->userdata[get_current_section($this,true)]['user_id']
        );
        $this->db->where('id', $id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }

    /**
     * Function get_category_with_child to return categories with all child
     */
    public function get_category_with_child($language_id = '', $parent_id=0, $level='')
    {
        $language_id = intval($language_id);
        $parent_id = intval($parent_id);
        $level = strval($level);

        if ($this->module_id != "")
        {
            $this->db->where('c.module_id =', $this->module_id);
        }

        $this->db->select('c.*, l.*, cm.id, cm.title');
        $this->db->from($this->_table . ' as c');
        $this->db->join(TBL_LANGUAGES . ' as l', 'c.lang_id = l.id', 'left');
        $this->db->join(TBL_CATEGORY_MODULES . ' as cm', 'c.module_id = cm.id', 'left');

        if ($language_id != '')
        {
            $this->db->where("c.lang_id", $language_id);
        }
        $this->db->where('c.parent_id', $parent_id);
        $this->db->where('c.status !=', -1);
        $query = $this->db->get();
        $records = $this->db->custom_result($query);

        if ($this->first_option != "")
        {
            $this->category_array[0] = $this->first_option;
        }
        if($records)
        {
            foreach($records as $k => $v)
            {
                if($level!='')
                    $padding = str_repeat('&nbsp;&nbsp;&nbsp;- ', $level);
                else
                    $padding = '';
                $this->category_array[$v['c']['category_id']] = $padding.''.$v['c']['title'];
                $child = $this->get_category_with_child($language_id, $v['c']['category_id'], $level+1);
                if($child)
                {
                    $this->category_array[$v['c']['category_id']] = $padding.''.$v['c']['title'];
                }
            }
        }
        return $this->category_array;
    }

    /**
     * Function get_category_module_list to return category module list
     */
    public function get_category_module_list()
    {
        $this->db->select('cm.id, cm.title')
                ->from(TBL_CATEGORY_MODULES . ' as cm')
                ->where('cm.status !=', -1);
        $query = $this->db->get();
        $records = $this->db->custom_result($query);

        foreach($records as $k => $v)
        {
            $this->module_array[$v['cm']['id']] = $v['cm']['title'];
        }

        return $this->module_array;
    }

    /**
     * Function get_module_detail_by_id to get module detail
     */
    public function get_module_detail_by_id($id)
    {
        $id = intval($id);

        $this->db->select('cm.*')
                ->from(TBL_CATEGORY_MODULES . ' as cm')
                ->where('cm.status !=', -1);
        $query = $this->db->get();
        $records = $this->db->custom_result($query);
    }

    /**
     * Function inactive_all_child_category to inactive all child category
     */
    function inactive_all_child_category($parent_category_id, $lang_id, $module_id)
    {
        $id = intval($id);

        $data_array = array(
            'status' => 0,
            'modified' => GetCurrentDateTime(),
            'modified_by' => $this->session->userdata[get_current_section($this,true)]['user_id']
        );
        $this->db->where('module_id', $module_id);
        $this->db->where('parent_id', $parent_category_id);
        $this->db->where('lang_id', $lang_id);
        $this->db->set($data_array);
        $this->db->update($this->_table);
    }
 /**
     * Function inactive_records to inactive records
     * @param array $id
     */
    public function inactive_records($id = array())
    {
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_table);

        return $id;
    }

    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records()
    {
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->update($this->_table);

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
        $this->db->update($this->_table);

        return $id;
    }

    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records()
    {
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_table);

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
        return $this->db->update($this->_table);
    }
}