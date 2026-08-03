<?php
/**
 *  Forum Model (actual table -  activity_log)
 *
 *  To perform queries related to  Forum management.
 *
 * @package CIDemoApplication
 * @subpackage Forum
 * @author AVSH
 */

class Forum_activity_log_model extends Base_Model
{
    protected $_tbl_forum_activity_log_model = TBL_ACTIVITY_LOG;
    /*
     * get view count
     */
     public function get_view_count($uri) {
        $this->db->select('count(distinct(ip_address)) as total');
        $this->db->from($this->_tbl_forum_activity_log_model);
        $this->db->like('url',$uri);
        $query = $this->db->get();
        
        $result = $this->db->custom_result($query);
        return $result[0];
     }
}
