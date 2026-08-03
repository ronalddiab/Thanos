<?php

/**
 *  Setting Model
 *
 *  To perform queries related to General Settings.
 *
 * @package CIDemoApplication
 * @subpackage URLs
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Settings_model extends Base_Model
{

    protected $_tabelname = TBL_SETTINGS;
    protected $_tbl_custom_settings = 'custom_settings';
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";

    /**
     * create an instance
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Function get_settings to fetch all settings list
     * @param type $id
     * @return type
     */
    function get_settings()
    {
        if($this->search_term != "")
        {
            $this->db->like('LOWER(s.setting_title)', strtolower($this->search_term), 'both');
        }
        if($this->sort_by != "" && $this->sort_order != "")
        {
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if(isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
        {
           $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select('s.*');
        $this->db->from($this->_tabelname . ' AS s');
        $result = $this->db->get();
        if(isset($this->_record_count) && $this->_record_count == true)
        {
            return count($this->db->custom_result($result));
        }
        else
        {
            return $this->db->custom_result($result);
        }
    }

    /**
     * Function get_setting_by_id to fetch all records of setting by id
     * @param integer $id
     */
    function get_setting_by_id($id)
    {
        //Type Casting
        $id = intval($id);

        $this->db->select('s.*');
        $this->db->from($this->_tabelname . ' As s');
        $this->db->where('s.id', $id);
        $result = $this->db->get();
        return $this->db->custom_result($result);
    }

    /**
     * save_settings() save setting detail
     * @param type $data
     * @return type $data
     */
    public function save_settings($data)
    {
        //Type Casting
        $id = intval($data['id']);
        try
        {
            if($id != 0)
            {
                $this->db->where('id', $id);
                $this->db->update($this->_tabelname, $data);
            }
            else
            {
                $this->db->insert($this->_tabelname, $data);
                $id = $this->db->insert_id();
            }
            return $id;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * for deleting setting constant
     * @param type $id
     * @return integer
     */
    public function delete_settings($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->delete($this->_tabelname);
        return $result;
    }

    /**
     * Function get_settings to fetch all settings list
     * @param type $id
     * @return type
     */
    function get_settings_fields()
    {
        $this->db->select('s.setting_label,s.setting_value');
        $this->db->from($this->_tbl_custom_settings . ' AS s');
        $query = $this->db->get();
        
        $results = $query->result_array();
        
        $setting_array = array();
        if(!empty($results))
        {
            foreach($results as $result)
            {
                $setting_array[$result['setting_label']] = $result['setting_value'];
            }
        }
        
        return $setting_array;
    }
    
    /**
     * save_settings() save setting detail
     * @param type $data
     * @return type $data
     */
    public function save_custom_settings($data = array())
    {   
        if(!empty($data))
        {
            foreach($data as $setting_label => $setting_value)
            {
                $this->db->set('setting_value', $setting_value);
                $this->db->where('setting_label', $setting_label);
                $this->db->update($this->_tbl_custom_settings);
            }
        }
    }

}

