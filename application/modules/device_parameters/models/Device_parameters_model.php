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
class Device_parameters_model extends Base_Model
{
	protected $_tbl_device_parameters = TBL_DEVICE_PARAMETERS;
	protected $_tbl_zones = TBL_ZONES;
	protected $_tbl_site_zones = TBL_SITE_ZONES;
	public $search_term = "";
	public $sort_by = "";
	public $sort_order = "";
	public $_record_count;


	function get_device_data($siteID)
	{
		$siteID = intval($siteID);

		$this->db->select('d.*, sz.*, z.zone_name');
		$this->db->from($this->_tbl_devices . ' AS d');
		$this->db->join($this->_tbl_zones . ' as z', 'd.zone_id = z.id', 'inner');
		$this->db->join($this->_tbl_site_zones . ' as sz', 'sz.zone_id = z.id', 'inner');
		$this->db->where('z.status', 1);
		$this->db->where('sz.site_id', $siteID);
		$query = $this->db->get();

		$siteZoneData = $query->result_array();

		if(!empty($siteZoneData))
		{
			return $siteZoneData;
		}
		else
		{
			return array();
		}
	}
	
	
    /**
     * Function save_records to add/update data
     */
    public function save_records($data)
    {
        if ($data['id'])
        {
            $this->db->where('id', $data['id']);
            $this->db->update($this->_tbl_device_parameters, $data);
            $id = $data['id'];
        }
        else
        {
            $this->db->insert($this->_tbl_device_parameters, $data);
            $id = $this->db->insert_id();
        }

        return $id;
    }

}