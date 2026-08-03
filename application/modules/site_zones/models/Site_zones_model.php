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
class Site_zones_model extends Base_Model
{
	protected $_tbl_site_zones = TBL_SITE_ZONES;
	protected $_tbl_zones = TBL_ZONES;
	public $search_term = "";
	public $sort_by = "";
	public $sort_order = "";
	public $_record_count;

	function get_site_zone_listing($siteID)
	{
		$siteID = intval($siteID);

		if($this->search_term != "")
		{
			$this->db->like("LOWER(s.site_location_name)", strtolower($this->search_term));
		}
		if($this->sort_by != "" && $this->sort_order != "")
		{
			$this->db->order_by($this->sort_by, $this->sort_order);
		}
		if(isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true)
		{
			$this->db->limit($this->record_per_page, $this->offset);
		}

		$this->db->select('sz.*, z.zone_name');
		$this->db->from($this->_tbl_site_zones . ' AS sz');
		$this->db->join($this->_tbl_zones . ' as z', 'sz.zone_id = z.id', 'left');
		$this->db->where('z.status', 1);
		$this->db->where('sz.site_id', $siteID);
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

	function get_site_zone_data($siteID)
	{
		$siteID = intval($siteID);

		$this->db->select('sz.*, z.zone_name');
		$this->db->from($this->_tbl_site_zones . ' AS sz');
		$this->db->join($this->_tbl_zones . ' as z', 'sz.zone_id = z.id', 'left');
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

}