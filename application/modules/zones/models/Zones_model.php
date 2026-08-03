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
class Zones_model extends Base_Model
{
	protected $_tbl_zones = TBL_ZONES;
	public $search_term = "";
	public $sort_by = "";
	public $sort_order = "";
	public $_record_count;

	function get_site_listing($site_id, $role_id)
	{

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

		$this->db->select('s.*, r.region_name,h.hotel_name,c.country');
		$this->db->from($this->_tbl_zones . ' AS s');
		$this->db->join($this->_tbl_regions . ' as r', 's.region_id = r.id', 'left');
		$this->db->join($this->_tbl_countries . ' AS c', 's.country_id = c.id', 'left');
		$this->db->join($this->_tbl_hotels . ' as h', 's.hotel_id = h.id', 'left');
		$this->db->where('s.status !=', -1);
		if($role_id != 1)
		{
			$this->db->where('s.id', $site_id);
		}
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
	 * Function delete_site to delete site
	 * @param integer $id
	 */
	public function delete_site($id)
	{
		//Type Casting
		$id = intval($id);
		$this->db->where('id', $id);
		$this->db->set('status', '-1');
		return $this->db->update($this->_tbl_zones);
	}

	/**
	 * Function save_site to add/update site
	 * @param array $data for site table
	 * @param array $data_profile for site_profile table
	 */
	public function save_site($data)
	{
		if(isset($data['id']) && $data['id'] != 0 && $data['id'] != "")
		{
			$this->db->set('modify_on', 'NOW()', FALSE);
			$site_data['modify_by'] = $data['user_id'];
			$this->db->where('id', $data['id']);
			$this->db->update($this->_tbl_zones, $data);
			$id = $site_data['id'];
		}
		else
		{
			$site_data['modify_by'] = $data['user_id'];
			$site_data['created_by'] = $data['user_id'];
			$this->db->set('modify_on', 'NOW()', FALSE);
			$this->db->set('created_on', 'NOW()', FALSE);
			if($this->db->insert($this->_tbl_zones, $site_data))
			{
				$id = $this->db->insert_id();
			}
		}
		return $id;
	}

	/**
	 * Function inactive_records to inactive records
	 * @param array $id
	 */
	public function inactive_records($id = array())
	{
		$this->db->set('s.modify_on', 'NOW()', FALSE);
		$this->db->set('s.status', 0);
		$this->db->where_in('s.id', $id);
		$this->db->update($this->_tbl_zones . ' AS s');
		/* $this->db->set('u.status', 0);
		  $this->db->where_in('u.site_id', $id);
		  $this->db->where('u.id !=', 1);
		  $this->db->update($this->_tbl_users . ' AS u'); */
		return $id;
	}

	/**
	 * Function inactive_all_records to inactive all records without deleted records
	 */
	public function inactive_all_records($site_id, $role_id)
	{
		$this->db->set('s.modify_on', 'NOW()', FALSE);
		$this->db->set('s.status', 0);
		$this->db->where('s.status !=', -1);

		if($role_id != 1)
		{
			$this->db->where('s.id', $site_id);
		}

		$this->db->update($this->_tbl_zones . ' AS s');
		/* $this->db->set('u.status', 0);
		  $this->db->where('u.site_id != ', 0);
		  $this->db->where('u.role_id != ', 1);
		  $this->db->where('u.role_id != ', 2);
		  $this->db->where('u.status !=', -1);
		  $this->db->update($this->_tbl_users . ' AS u'); */
		return true;
	}

	/**
	 * Function active_records to active records
	 * @param array $id
	 */
	public function active_records($id = array())
	{
		$this->db->set('s.modify_on', 'NOW()', FALSE);
		$this->db->set('s.status', 1);
		$this->db->where_in('s.id', $id);
		$this->db->update($this->_tbl_zones . ' AS s');
		/* $this->db->set('u.status', 1);
		  $this->db->where_in('u.site_id', $id);
		  $this->db->where('u.id !=', 1);
		  $this->db->update($this->_tbl_users . ' AS u'); */
		return $id;
	}

	/**
	 * Function active_all_records to active all records without deleted records
	 */
	public function active_all_records($site_id, $role_id)
	{
		$this->db->set('s.modify_on', 'NOW()', FALSE);
		$this->db->set('s.status', 1);
		$this->db->where('s.status !=', -1);
		if($role_id != 1)
		{
			$this->db->where('s.id', $site_id);
		}
		$this->db->update($this->_tbl_zones . ' As s');
		/* $this->db->set('u.status', 1);
		  $this->db->where('u.site_id !=', 0);
		  $this->db->where('u.role_id !=', 1);
		  $this->db->where('u.role_id !=', 2);
		  $this->db->where('u.status !=', -1);
		  $this->db->update($this->_tbl_users . ' AS u'); */
		return true;
	}

	/**
	 * Function delete_records to delete URL
	 * @param integer $id
	 */
	public function delete_records($id = array())
	{
		$this->db->set('modify_on', 'NOW()', FALSE);
		$this->db->where_in('id', $id);
		$this->db->set('status', '-1');
		$this->db->set('deleted_by', $this->user_id, FALSE);
		$this->db->set('deleted_on', 'NOW()', FALSE);
		return $this->db->update($this->_tbl_zones);
	}

	function get_sites()
	{
		$this->db->select("id");
		$this->db->from($this->_tbl_zones);
		$this->db->where('status =', 1);
		$result = $this->db->get();
		return $result->result_array();
	}
}