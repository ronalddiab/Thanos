<?php
/**
 *  Modulebuilder Model
 *
 *  To perform queries related module builder
 * 
 * @package CIDemoApplication
 * @subpackage Roles
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */

class Modulebuilder_model extends Base_Model
{
    protected $_tbl_modules = TBL_MODULE;
    protected $_tbl_module_permission = TBL_ROLE_PERMISSION;
    protected $_tbl_user_permission = TBL_USER_PERMISSION;
    public $search_term ="";
    public $sort_by ="";
    public $sort_order =""; 
    
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Function get_module_listing to fetch all records of modules
     */
    function get_module_listing()
    {
        if(isset($this->search_term) && $this->search_term != "")
        {
            $this->db->like("LOWER(M.module_name)", strtolower($this->search_term));
        }
        if(isset($this->sort_by) && $this->sort_by != "" && $this->sort_order != "")
        {             
            $this->db->order_by('M.'.$this->sort_by, $this->sort_order); 
        }
        if(isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
           $this->db->limit($this->record_per_page, $this->offset);
        }
        
        $this->db->select('M.*');
        $this->db->from($this->_tbl_modules.' AS M');
        $query  = $this->db->get();
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
     * Function create_table to create table 
     * @param array $data 
     */
    public function create_table($data)
    {
        $this->db->query($data);
        
        return true;
    }

    /**
     * Function save_module to add/update modules 
     * @param array $data 
     */
    public function save_module($data)
    {
        //Type Casting 
        $id = intval($data['id']);
        
        if ($id != 0 && $id != "")
        {
            $this->db->where('id', $id);
            $this->db->update($this->_tbl_modules, $data);
            $id = $id;
        } else
        {
            $this->db->insert($this->_tbl_modules, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    /**
     * Function delete_module to delete modules 
     * @param int $id 
     */
    public function delete_module($id)
    {
        //Type Casting 
        $id = intval($id);
        
        $this->db->where('id', $id);
        return $this->db->delete($this->_tbl_modules);
    }
    
    /**
     * Function inactive_records to inactive records
     * @param array $id 
     */
    public function inactive_records($id = array())
    {        
        $this->db->set('status', 0);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_modules);

        return $id;
    }
    
    /**
     * Function inactive_all_records to inactive all records without deleted records
     */
    public function inactive_all_records()
    {        
        $this->db->set('status', 0);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_modules);

        return $id;
    }
    
    /**
     * Function active_records to active records
     * @param array $id 
     */
    public function active_records($id = array())
    {        
        $this->db->set('status', 1);
        $this->db->where_in('id', $id);
        $this->db->update($this->_tbl_modules);

        return $id;
    }
    
    /**
     * Function active_all_records to active all records without deleted records
     */
    public function active_all_records()
    {        
        $this->db->set('status', 1);
        $this->db->where('status !=', -1);
        $this->db->update($this->_tbl_modules);

        return $id;
    }

    /**
     * Function delete_records to delete URL
     * @param integer $id 
     */
    public function delete_records($id = array())
    {
        $this->db->where_in('id', $id);
        return $this->db->delete($this->_tbl_modules);
    }
}

