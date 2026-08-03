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
class Parameters_model extends Base_Model
{
	protected $_tbl_parameters = TBL_PARAMETERS;
	public $search_term = "";
	public $sort_by = "";
	public $sort_order = "";
	public $_record_count;


	function get_parameters_data()
	{
		$this->db->select('p.*');
		$this->db->from($this->_tbl_parameters . ' AS p');
		$this->db->limit(9, 18);
		$this->db->where('p.status', 1);
		$query = $this->db->get();

		$parametersData = $query->result_array();

		if(!empty($parametersData))
		{
			return $parametersData;
		}
		else
		{
			return array();
		}
	}

}