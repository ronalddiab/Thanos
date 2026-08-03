<?php
/**
 *  Cms meta Model
 *
 *  To perform queries related to cms meta tags.
 * 
 * @package CIDemoApplication
 * @subpackage Cms
 * @copyright	(c) 2013, TatvaSoft
 * @author Amit Patel AMPT
 */

class Cms_Meta_model extends Base_Model {

    protected $_table = TBL_CMS_META;
    public $meta_title="";
    public $meta_keywords="";
    public $meta_description="";

    function __construct() {
        parent::__construct();
    }

    /**
     * Function insert_cms_meta to insert cms meta record
     */
    function insert_cms_meta($cms_id,$lang_id) {
        
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);
        
        $data_array = array();

        $data_array['cms_id'] = $cms_id;
        $data_array['lang_id'] = $lang_id;
        
        if(isset($this->meta_title) )
        {
            $data_array['meta_title'] = $this->meta_title;
        }
        if(isset($this->meta_keywords) )
        {
            $data_array['meta_keywords'] = $this->meta_keywords;
        }
        if(isset($this->meta_description) )
        {
            $data_array['meta_description'] = $this->meta_description;
        }
        
        $this->db->set($data_array);
        $this->db->insert($this->_table);        
        // return $this->db->_error_number(); // return the error occurred in last query
        $error =  $this->db->error(); // return the error occurred in last query
        if(isset($error['message']) && $error['message'] != ''){
            return $error['message'];
        }     
        return true;
    }
    
    /**
     * Function update_cms_meta to update cms meta record
     */
    function update_cms_meta($cms_id,$lang_id) {
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);
        
        if(isset($this->meta_title) )
        {
            $data_array['meta_title'] = $this->meta_title;
        }
        if(isset($this->meta_keywords) )
        {
            $data_array['meta_keywords'] = $this->meta_keywords;
        }
        if(isset($this->meta_description) )
        {
            $data_array['meta_description'] = $this->meta_description;
        }
        
        $this->db->where(array('cms_id' => $cms_id, 'lang_id'=>$lang_id));
        $this->db->set($data_array);
        $this->db->update($this->_table);                
    }
    
    /**
     * Function is_cms_meta_exist to check cms meta record exist or not
     */
    public function is_cms_meta_exist($cms_id, $lang_id) {
        $cms_id = intval($cms_id);
        $lang_id = intval($lang_id);
        
        $this->db->select('*')
                 ->from(TBL_CMS_META)
                 ->where(array('c.cms_id' => $cms_id, 'c.lang_id' => $lang_id));
        $query = $this->db->get();          
        return $query->num_rows;
    }

}
