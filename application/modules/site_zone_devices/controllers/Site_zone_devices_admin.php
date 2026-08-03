<?php
/**
 *  Hotel Admin Controller
 *
 *  To perform site management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
if(!defined('BASEPATH'))
	exit('No direct script access allowed');
class Site_zone_devices_admin extends Base_Admin_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->breadcrumb->add(lang('site-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites');
		// Login check for admin
		$this->access_control($this->access_rules());
	}

	/**
	 * Function access_rules to check login
	 */
	public function access_rules()
	{
		return array(
			array(
				'actions' => array('index'),
				'users' => array('@'),
			)
		);
	}

	/**
	 * Function index to view listing of sites
	 */
	function index()
	{
		$data = array();

		//Render view
		$this->theme->view($data);
	}

}