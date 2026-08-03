<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 *  Base Model
 *
 *  base model to use general model related function to use in whole site.
 * 
 * @package CIDemoApplication
 *  
 * @copyright	(c) 2013, TatvaSoft
 * @author Amit Patel <amit.patel@sparsh.com>
 */
class Base_Model extends CI_Model
{
    public $language_id;
    public $_record_count;
    public function __construct()
    {
        parent::__construct();
        $this->base_model_val = "Base model";
        $this->db = MY_DB();
    }

    /**
     * Function sample_test_by_id for sample module.
    */
    public function sample_test_by_id($id = '', $return_type = 0)
    {
        if ($id === FALSE)
        {
            return FALSE;
        }
        $query = $this->db->get_where($this->table, array('sample_id' => $id));

        if ($query->num_rows())
        {
            if ($return_type == 0)
            {
                return $query->row();
            }
            else
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }
    
    

}
