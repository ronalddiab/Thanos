<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Languages Model
 *
 *  To perform queries related to Languages.
 *
 * @package CIDemoApplication
 * @subpackage URLs
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Languages_model extends Base_Model
{

    protected $_tabelname = TBL_LANGUAGES;
    protected $_cmstable = TBL_CMS;
    public $search_term = "";
    public $sort_by = "";
    public $sort_order = "";

    function __construct()
    {
        parent::__construct();
    }

    /**
     * get_languages() function for getting all languages details
     * @param integer $id default = 0
     * @param integer $status default NULL
     */
    function get_languages($id = 0, $status = NULL)
    {
        if($id > 0)
        {
            $this->db->where("l.id", $id);
        }

        if($status != NULL)
        {
            $this->db->where("l.status", $status);
        }

        $this->db->select("l.*");
        $this->db->from($this->_tabelname . " as l");
        $this->db->where('l.status = ', 1);
        $query = $this->db->get();
        //echo $this->db->last_query(); exit;
        return $this->db->custom_result($query);
    }

    /**
     * Function get_languages_list to fetch all languages list
     * @param type $id
     * @return type
     */
    function get_languages_list()
    {
        if($this->sort_by != "" && $this->sort_order != "")
        {
            $this->sort_by = $this->sort_by;
            $this->db->order_by($this->sort_by, $this->sort_order);
        }
        if(isset($this->record_per_page) && isset($this->offset))
        {
            $this->db->limit($this->record_per_page, $this->offset);
        }

        $this->db->select("l.*");
        $this->db->from($this->_tabelname . " as l");
        $this->db->where("l.status != ", -1);


        $query = $this->db->get();

        //echo $this->db->last_query();exit;

        return $this->db->custom_result($query);
    }

    /**
     * Function get_setting_by_id to fetch all records of setting by id
     * @param integer $id
     */
    function get_languages_by_id($id = NULL)
    {
        //Type Casting
        $id = intval($id);

        $this->db->select('l.*');
        $this->db->from($this->_tabelname . ' AS l');
        $this->db->where('l.id', $id);
        $query = $this->db->get();

        return $this->db->custom_result($query);
    }

    /**
     * save_languages() function for saving languages detail
     * @param array $data
     */
    public function save_languages($data)
    {

        $id = intval($data['id']);
        if($data['id'] != 0)
        {
            $this->db->where('id', $id);
            $this->db->update($this->_tabelname, $data);
        }
        else
        {
            $this->db->insert($this->_tabelname, $data);
            $id = $this->db->insert_id();
        }

        //check for default status update
        if(isset($data['default']) && $data['default'] != 0)
        {
            $this->db->where("id != ", $id);
            $this->db->update($this->_tabelname, array('default' => 0));
        }

        return $id;
    }

    /**
     * delete_languages() function for saving languages detail
     * @param array $data
     */
    public function delete_languages($id)
    {
        //check default language
        $this->db->where("default", "1");
        $this->db->where('id', $id);
        $result = $this->db->get($this->_tabelname)->row();


        if(empty($result))
        {
            //check cms table where language use
            $this->db->select('id');
            $this->db->from($this->_cmstable);
            $this->db->where('lang_id', $id);
            $this->db->where('status', 1);
            $result = $this->db->get()->num_rows();
            $this->db->select('*');
            $this->db->from($this->_tabelname);
            $this->db->where('id', $id);
            $this->db->where('status', 1);
            $lang_data = $this->db->get()->row();



            if($result == 0)
            {
                $path = FCPATH.'application'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$lang_data->language_name;
                $this->load->helper("file"); // load the helper
                delete_files($path);
                rmdir($path); // delete all files/folders
                $this->db->where('id', $id);
                $result = $this->db->delete($this->_tabelname);

            }
            else
            {
                $result = -1; //for dependancy table error message
            }
            return $result;
        }
        else
        {
            return 0;
        }
    }

    /**
     * getalllanguages() function for getting all languages
     * @return array $langArr
     */
    public function getalllanguages()
    {
        $this->db->select("l.*");
        $this->db->from($this->_tabelname . ' AS l');
        $this->db->where("l.status", 1);
        $query = $this->db->get();
        $result = $this->db->custom_result($query);
        $langArr = array();
        foreach ($result as $key => $val)
        {
            //take alias from an array
            $alias = end(array_keys($val));

            $langArr[$val[$alias]['id']] = strtolower($val[$alias]['language_code']);
            $langArr[strtolower($val[$alias]['language_code'])] = strtolower($val[$alias]['language_name']);
        }
        return $langArr;
    }

    /*
     * Get default language
     * @return  array
     */

    function get_default_language()
    {
        $this->db->select('l.*');
        $this->db->from($this->_tabelname . ' as l');
        $this->db->where("l.default", "1");
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /*
     * Get langugae detail by name
     * @param string $lang_name
     * @return  array
     */

    function get_languages_by_name($lang_name)
    {
        $this->db->where("language_name", trim(strip_tags($lang_name)));
        $query = $this->db->get($this->_tabelname);
        return $query->result_array();
    }

    /*
     * Get langugae detail by code
     * @param string $lang_code
     * @return  array
     */

    function get_languages_by_code($lang_code)
    {
        $this->db->select("l.*");
        $this->db->from($this->_tabelname . " as l");
        $this->db->where("language_code", $lang_code);
        $query = $this->db->get();
        return $this->db->custom_result($query);
    }

    /**
     * For getting no of record(s) count
     * @return integer
     */
    public function record_count()
    {

        $this->db->select('l.*');
        $this->db->from($this->_tabelname.' AS l');
        $this->db->where("l.status != ", -1);

        $result = $this->db->get()->num_rows();

        return $result;
    }

}

