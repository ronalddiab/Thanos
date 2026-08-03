<?php
/**
 *  Zones Model
 *
 *  To perform queries related to Zone management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class Site_zone_devices_model extends Base_Model
{
	protected $_tbl_site_zone_devices = TBL_SITE_ZONE_DEVICES;
	public $search_term = "";
	public $sort_by = "";
	public $sort_order = "";
	public $_record_count;

	
    /**
     * Function save_records to add/update data
     */
    public function save_records($data)
    {
        if ($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update($this->_tbl_site_zone_devices, $data);
            $id = $data['id'];
        }
        else
        {
            $this->db->insert($this->_tbl_site_zone_devices, $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }
}