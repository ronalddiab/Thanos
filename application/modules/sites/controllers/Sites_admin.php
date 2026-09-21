<?php



/**

 *  Hotel Admin Controller

 *

 *  To perform site management.

 *

 * @package CIDemoApplication

 * @subpackage Users

 * @copyright    (c) 2013, TatvaSoft

 * @author panks

 */

if (!defined('BASEPATH')) {

	exit('No direct script access allowed');
}



class Sites_admin extends Base_Admin_Controller

{


// GARIMA TEST DEPLOYMENT
	public $search_term;

	private $site_id                 = '';

	private $role_id                 = '';

	private $user_id                 = '';

	public function __construct()
	{

		parent::__construct();

		$this->load->library('form_validation');
		$this->breadcrumb->add(lang('site-management'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites');
		// Login check for admin
		$this->access_control($this->access_rules());
		$this->language = $this->uri->segment(4);
		$this->load->library('unit_test');
		$this->load->model('utilities/utilities_model');
		$this->load->model('import/import_model');
		$this->load->model('sites/sites_model');
		$this->load->model('users/users_model');
		$this->load->model('sites/site_waste_model');

		$this->site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : '';
		$this->role_id = isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : '';
		$this->user_id = isset($this->session->userdata[$this->section_name]['user_id']) ? $this->session->userdata[$this->section_name]['user_id'] : 0;
		//$this->breadcrumb->add(lang('import_measure'), base_url() . 'import_measure');
	}



	/**

	 * Function access_rules to check login

	 */

	public function access_rules()

	{



		return array(

			array(
				'actions' => array('index', 'edit', 'add', 'action', 'delete', 'save', 'view_data', 'set_user_theme', 'set_notification', 'pdf', 'cron_settings', 'get_sites', 'waste', 'emission', 'delete_emission_image', 'view_area_history', 'residence', 'export_waste', 'replicate_residence', 'export_utility', 'permission', 'export_prev_utility', 'setSiteNotificationManually', 'export_site_info', 'export_utility_choices', 'export_utility_invoices','export_utility_last_updated_log', 'group_utility_report', 'export_group_waste_corporate_report', 'download_corporate_utilities_dashboard'),
				'users'   => array('@'),

			),

		);
	}




	public function permission()
	{
		// Group Admin - Role 2
		// $arrayUsers = [3,7,8,11,14,16,17,20,24,27,28,30,33,36,43,44,49,56,62,72,74,75,76,78,80,82,86,91,94,114,123,125,162,173,180,181,185,196,198,206,208,212,213,244,245,250,261,267,286,299,303,306,310,312,313,314,315,316,317,318,325,326,364,365,366,367,368,369,370,371,372,373,386,387,389,390,391,403,411,417,419,422,424,427,436,437,438,441,444,447,453,455,459,460,462,464,467,468,470,473,476,481,484,487,495,496,497,515,516,518,521,528,555,560,565,568,569,591,592,611,642,643,644,646,647,658,680,684,698,718,740,748,749];
		// $arrayUsersPermissions = [2,4,15,16,37,42,45,54,134,262,326,329,338,342,343,344,351,352,353,354,355,356,357,358,359,360,366,367,368,372,375,376,381,382,383,384,385,386,387,388,390,391,392,395,396,397,398,399,400,402,403,404,406,410,411,412,414,415,423,425,427,429];

		// Site Admin User - Role 3
		$arrayUsers = [2, 4, 5, 9, 10, 12, 13, 18, 21, 22, 25, 26, 29, 31, 32, 34, 35, 53, 59, 60, 61, 63, 64, 65, 66, 67, 68, 69, 70, 73, 77, 79, 81, 83, 84, 85, 87, 88, 89, 90, 92, 93, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 115, 116, 117, 118, 119, 120, 121, 122, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 154, 155, 156, 157, 158, 159, 160, 161, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 176, 177, 178, 179, 182, 183, 184, 186, 187, 188, 190, 191, 192, 193, 194, 195, 197, 199, 200, 201, 202, 203, 204, 205, 207, 210, 211, 214, 215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 239, 240, 241, 242, 243, 246, 247, 248, 249, 251, 252, 253, 254, 255, 256, 257, 258, 259, 260, 262, 263, 264, 265, 266, 268, 269, 270, 271, 272, 273, 274, 275, 276, 277, 279, 280, 281, 282, 283, 284, 285, 287, 288, 289, 290, 291, 292, 293, 294, 295, 296, 297, 298, 300, 301, 302, 304, 305, 307, 308, 309, 311, 319, 320, 321, 322, 323, 324, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 404, 405, 407, 408, 409, 410, 412, 413, 414, 415, 416, 418, 420, 421, 423, 425, 426, 428, 429, 430, 431, 432, 433, 434, 439, 440, 442, 443, 445, 446, 448, 449, 450, 451, 452, 454, 456, 457, 458, 461, 465, 466, 469, 471, 472, 474, 475, 477, 478, 479, 480, 482, 483, 486, 490, 491, 492, 493, 494, 498, 499, 500, 503, 504, 505, 506, 507, 508, 509, 510, 512, 513, 514, 519, 520, 522, 523, 524, 525, 526, 527, 529, 530, 531, 532, 533, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 552, 553, 554, 556, 557, 558, 559, 561, 562, 563, 564, 566, 567, 570, 571, 572, 576, 577, 578, 580, 581, 582, 583, 584, 585, 586, 587, 589, 590, 593, 594, 595, 596, 599, 600, 601, 602, 603, 604, 605, 606, 607, 608, 609, 610, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627, 628, 629, 630, 631, 632, 633, 634, 635, 636, 637, 638, 639, 640, 641, 645, 648, 649, 651, 652, 653, 654, 655, 656, 657, 659, 660, 661, 662, 663, 664, 665, 666, 667, 668, 669, 670, 671, 672, 673, 674, 675, 676, 677, 678, 679, 681, 682, 683, 685, 686, 687, 688, 689, 690, 691, 692, 693, 694, 695, 696, 697, 699, 700, 701, 702, 703, 704, 705, 708, 714, 715, 716, 717, 722, 723, 727, 728, 729, 730, 731, 732, 733, 734, 735, 736, 741, 742, 743, 744, 745, 746, 747, 750, 751, 752, 753, 754, 755, 756, 757, 758, 759, 760, 761, 762, 764, 765, 766, 767, 768, 769, 770, 771, 772, 773];
		$arrayUsersPermissions = [2, 4, 16, 37, 42, 54, 326, 329, 338, 342, 343, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 366, 367, 368, 372, 375, 376, 381, 382, 383, 384, 385, 386, 388, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 403, 404, 406, 410, 411, 412, 414, 415, 421, 423, 427, 429];


		foreach ($arrayUsers as $userkey => $uservalue) {
			$this->users_model->delete_permissions_to_user($uservalue);
			foreach ($arrayUsersPermissions as $permissionkey => $permissionvalue) {
				$this->users_model->insertPermission($uservalue, $permissionvalue);
			}
		}

		echo "Done";
		exit;
	}



	/**

	 * Function index to view listing of sites

	 */

	public function index()

	{

		//Paging parameters

		$offset                             = get_offset($this->page_number, $this->record_per_page);

		$this->sites_model->record_per_page = $this->record_per_page;

		$this->sites_model->offset          = $offset;



		$site_id = $this->session->userdata[$this->section_name]['site_id'];

		$role_id = $this->session->userdata[$this->section_name]['role_id'];

		$user_id = $this->session->userdata[$this->section_name]['user_id'];



		$this->sites_model->site_id = $this->site_id;

		$this->sites_model->role_id = $this->role_id;

		$this->sites_model->user_id = $this->user_id;



		//set sort/search parameters in pagging

		if ($this->input->post()) {



			$data = $this->input->post();

			// Search Term ***

			if (isset($data['search_term']) && !empty($data['search_term'])) {
				$this->sites_model->search_term = trim($data['search_term']);

				$this->session->set_custom_userdata($this->section_name, "site_search_term", $this->input->post('search_term'));
			} else {

				$this->session->set_custom_userdata($this->section_name, "site_search_term", "");
			}

			// Search Term ***

			// Sort Order ***

			if (isset($data['sort_by']) && $data['sort_order']) {

				$this->sites_model->sort_by    = $data['sort_by'];

				$this->sites_model->sort_order = $data['sort_order'];

				$this->session->set_custom_userdata($this->section_name, "site_sort_by", $this->input->post('sort_by'));

				$this->session->set_custom_userdata($this->section_name, "site_sort_order", $this->input->post('sort_order'));
			} else {

				$this->session->set_custom_userdata($this->section_name, "site_sort_by", "");

				$this->session->set_custom_userdata($this->section_name, "site_sort_order", "");
			}

			// Sort Order ***



			if (isset($data['type']) && $data['type'] == 'delete') {



				// Newly added

				$tempArr = array();

				foreach ($data['ids'] as $key => $val) {

					$tempArr[] = $val;
				}

				// Newly added

				if ($this->sites_model->delete_records($tempArr)) {

					echo $this->theme->message(lang('site-delete-success'), 'success');

					exit;
				}
			}



			if (isset($data['type']) && $data['type'] == 'active') {

				// Newly added

				$tempArr = array();

				foreach ($data['ids'] as $key => $val) {

					$tempArr[] = $val;
				}

				// Newly added

				if ($this->sites_model->active_records($tempArr)) {

					echo $this->theme->message(lang('site-active-success'), 'success');

					exit;
				}
			}

			if (isset($data['type']) && $data['type'] == 'inactive') {

				// Newly added

				$tempArr = array();

				foreach ($data['ids'] as $key => $val) {

					$tempArr[] = $val;
				}

				// Newly added

				if ($this->sites_model->inactive_records($tempArr)) {

					echo $this->theme->message(lang('site-inactive-success'), 'success');

					exit;
				}
			}

			if (isset($data['type']) && $data['type'] == 'active_all') {



				if ($this->sites_model->active_all_records($site_id, $role_id)) {

					echo $this->theme->message(lang('site-active-success'), 'success');

					exit;
				}
			}

			if (isset($data['type']) && $data['type'] == 'inactive_all') {

				if ($this->sites_model->inactive_all_records($site_id, $role_id)) {

					echo $this->theme->message(lang('site-inactive-success'), 'success');

					exit;
				}
			}



			if (isset($data['type']) && $data['type'] == 'get_all_sites') {

				$sites = $this->sites_model->get_site_listing_for_users($site_id, $role_id, $user_id);

				/* if ($role_id == 1) {

		$default_site[] = array('s'=>array('id'=>0,'site_location_name'=>'All sites'));

		$sites = array_merge($default_site,$sites);

		} */



				$result['sites'] = $sites;

				$result['count'] = count($sites);

				echo json_encode($result);

				exit;
			}
		}



		if (!empty($this->session->userdata[$this->section_name]['site_search_term'])) {

			$this->sites_model->search_term = trim($this->session->userdata[$this->section_name]['site_search_term']);
		}

		if (!empty($this->session->userdata[$this->section_name]['site_sort_by'])) {

			$this->sites_model->sort_by = $this->session->userdata[$this->section_name]['site_sort_by'];
		}

		if (!empty($this->session->userdata[$this->section_name]['site_sort_order'])) {

			$this->sites_model->sort_order = $this->session->userdata[$this->section_name]['site_sort_order'];
		}



		//Load data for url listing

		// $site_id = $this->session->userdata[$this->section_name]['site_id'];

		// $role_id = $this->session->userdata[$this->section_name]['role_id'];

		$sites                            = $this->sites_model->get_site_listing($this->site_id, $this->role_id, $this->user_id);
		$this->sites_model->_record_count = true;

		$total_records                    = $this->sites_model->get_site_listing($site_id, $role_id, $this->user_id);

		// Pass data to view file

		$this->search_term     = $this->sites_model->search_term;

		$data['sites']         = $sites;

		$data['page_number']   = $this->page_number;

		$data['total_records'] = $total_records;

		$data['search_term']   = $this->sites_model->search_term;

		$data['sort_by']       = $this->sites_model->sort_by;

		$data['sort_order']    = $this->sites_model->sort_order;



		//Create page-title

		$this->theme->set('page_title', lang('site-management'));



		//Render view

		$this->theme->view($data);
	}



	public function check_site_edit_unique($str, $field)

	{

		list($table, $field, $field1, $value1) = explode('.', $field);



		if (!empty($value1)) {

			$this->db->where($field1 . ' !=', $value1);
		}

		$this->db->where('status !=', -1);

		$query = $this->db->limit(1)->get_where($table, array($field => $str));



		$this->form_validation->set_message('check_site_edit_unique', lang('msg-site-name-already-exists'));

		return $query->num_rows() === 0;
	}



	/**

	 * Function sites_validation_rules to validate input

	 */

	public function sites_validation_rules()

	{

		$id = intval($this->input->post('id'));

		$this->form_validation->set_rules('site_color', lang('hotel-theme'), 'trim|required|max_length[25]');

		//$this->form_validation->set_rules('site_location_name', lang('location'), 'trim|required|max_length[50]');

		$this->form_validation->set_rules('site_location_name', lang('location'), 'trim|required|callback_check_site_edit_unique[sites.site_location_name.id.' . $id . ']');

		$this->form_validation->set_rules('city', lang('city'), 'trim|required|max_length[50]');
		$this->form_validation->set_rules('site_location_latitude', lang('latitude'), 'trim|max_length[10]');

		$this->form_validation->set_rules('site_location_longitude', lang('longitude'), 'trim|max_length[10]');
		$this->form_validation->set_rules('station_id', lang('weather-station'), 'trim|required|max_length[20]');
		$this->form_validation->set_rules('base_cdd_temprature', 'Base CDD temprature', 'trim|max_length[10]|numeric');

		$this->form_validation->set_rules('base_hdd_temprature', 'Base HDD temprature', 'trim|max_length[10]|numeric');

		$this->form_validation->set_rules('site_year_built', lang('year-built'), 'trim|required|integer|max_length[4]');
		$this->form_validation->set_rules('site_builtup_area', lang('total-built-up-area'), 'trim|numeric');
		$this->form_validation->set_rules('cooled_builtup_area', lang('cooled-built-up-area'), 'trim|numeric');
		$this->form_validation->set_rules('total_meeting_area', lang('total-meeting-area'), 'trim|numeric');

		$this->form_validation->set_rules('total_spa_area', lang('spa-area'), 'trim|numeric');
		$this->form_validation->set_rules('room_area_rental_program', lang('room-area-rental-program'), 'trim|numeric');
		$this->form_validation->set_rules('room_area_private_residence', lang('room-area-private-residence'), 'trim|numeric');
		$this->form_validation->set_rules('hotel_rooms_area', lang('hotel-rooms-area'), 'trim|numeric');
		$this->form_validation->set_rules('residential_common_area', lang('residential-common-area'), 'trim|numeric');
		$this->form_validation->set_rules('employee_living_quarters_area', lang('employee-living-quarters-area'), 'trim|numeric');
		$this->form_validation->set_rules('f_b_service', lang('f-b-service'), 'trim|numeric');
		$this->form_validation->set_rules('restaurant_area', lang('restaurant-area'), 'trim|numeric');
		$this->form_validation->set_rules('landscaped_area', lang('landscaped-area'), 'trim|numeric');
		$this->form_validation->set_rules('comments', lang('comments'), 'trim');
		$this->form_validation->set_rules('f_b_services_operated', lang('f-b-services-operated'), 'trim|required|numeric');
		$this->form_validation->set_rules('f_b_services_outsourced', lang('f-b-services-outsourced'), 'trim|required|numeric');
		$this->form_validation->set_rules('month_year_operation', lang('month-year-operation'), 'trim|required|numeric');
		$this->form_validation->set_rules('vehicle_electric', lang('vehicle-electric'), 'trim|required|numeric');
		$this->form_validation->set_rules('vehicle_petrol', lang('vehicle-petrol'), 'trim|required|numeric');
		$this->form_validation->set_rules('rental_program_residence', lang('rental-program-residence'), 'trim|numeric');
		$this->form_validation->set_rules('rental_private_residence', lang('rental-private-residence'), 'trim|numeric');
		$this->form_validation->set_rules('rental_program_residence_conditioned', lang('rental-program-residence-conditioned'), 'trim|numeric');
		$this->form_validation->set_rules('rental_private_residence_conditioned', lang('rental-private-residence-conditioned'), 'trim|numeric');
		$this->form_validation->set_rules('rental_program_residence_suites', lang('rental-program-residence-suites'), 'trim|required|numeric');
		$this->form_validation->set_rules('rental_private_residence_suites', lang('rental-private-residence-suites'), 'trim|required|numeric');
		$this->form_validation->set_rules('room_area_rental_program', lang('room-area-rental-program'), 'trim|required|numeric');
		$this->form_validation->set_rules('room_area_private_residence', lang('room-area-private-residence'), 'trim|required|numeric');
		$this->form_validation->set_rules('indoor_parking_area', lang('indoor-parking-area'), 'trim|numeric');
		$this->form_validation->set_rules('rooms_keys', lang('room-keys'), 'trim|integer|max_length[3]');
		$this->form_validation->set_rules('outdoor_pools', lang('outdoor-pools'), 'trim|integer');

		$this->form_validation->set_rules('indoor_pools', lang('indoor-pools'), 'trim|integer');

		//$this->form_validation->set_rules('substation[substation_name][]', lang('substation-name'), 'trim|required');

		$this->form_validation->set_rules('substation[substation_quantity][]', lang('substation-quantity'), 'trim|required|integer');

		$this->form_validation->set_rules('substation[substation_power][]', lang('substation-power'), 'trim|required|numeric');

		$this->form_validation->set_rules('generator[generator_name][]', lang('generators-name'), 'trim|required');

		$this->form_validation->set_rules('generator[generator_quantity][]', lang('generators-quantity'), 'trim|required|integer');

		$this->form_validation->set_rules('generator[generator_power][]', lang('generators-power'), 'trim|required|numeric');

		//$this->form_validation->set_rules('hot_water_boiler[hot_water_boiler_name][]', lang('hot-water-boiler-name'), 'trim|required');

		$this->form_validation->set_rules('hot_water_boiler[hot_water_boiler_quantity][]', lang('hot-water-bolier-quantity'), 'trim|required');

		$this->form_validation->set_rules('hot_water_boiler[hot_water_boiler_power][]', lang('hot-water-boiler-power'), 'trim|required|numeric');

		//$this->form_validation->set_rules('steam_boiler[steam_boiler_name][]', lang('steam-boiler-name'), 'trim|required');

		$this->form_validation->set_rules('steam_boiler[steam_boiler_quantity][]', lang('steam-bolier-quantity'), 'trim|required|integer');

		$this->form_validation->set_rules('steam_boiler[steam_boiler_power][]', lang('steam-boiler-power'), 'trim|required|numeric');

		$this->form_validation->set_rules('elcetrical_hw_total', lang('elcetrical-hw-total'), 'trim|required|integer');

		$this->form_validation->set_rules('calorifiers_unit', lang('calorifiers-unit'), 'trim|required|numeric');

		$this->form_validation->set_rules('calorifiers_volume', lang('calorifiers-volume'), 'trim|required|numeric');

		$this->form_validation->set_rules('elcetrical_hw_total_capacity', lang('elcetrical-hw-total-capacity'), 'trim|required|numeric');

		$this->form_validation->set_rules('elcetrical_hw_total_power', lang('elcetrical_hw_total_power'), 'trim|required|numeric');
		// $this->form_validation->set_rules('electricity_emission_factor', lang('electricity-emission-factor'), 'trim|required|numeric');
		// $this->form_validation->set_rules('fuel_emission_factor', lang('fuel-emission-factor'), 'trim|required|numeric');
		// $this->form_validation->set_rules('lpg_emission_factor', lang('lpg-emission-factor'), 'trim|required|numeric');
		// $this->form_validation->set_rules('natural_gas_emission_factor', lang('natural-gas-emission-factor'), 'trim|required|numeric');
		// $this->form_validation->set_rules('district_cooling_emission_factor', lang('lpg-emission-factor'), 'trim|required|numeric');
		// $this->form_validation->set_rules('district_heating_emission_factor', lang('lpg-emission-factor'), 'trim|required|numeric');
		//$this->form_validation->set_rules('stp_capacity', lang('stp-capacity'), 'trim|required|numeric');

		//$this->form_validation->set_rules('ro_plant_capacity', lang('ro-capacity'), 'trim|required|numeric');

		//$this->form_validation->set_rules('total_vrv', lang('total-vrv'), 'trim|required|numeric');

		//$this->form_validation->set_rules('total_split_dx_unit', lang('total-split-dx-unit'), 'trim|required|numeric');

		//$this->form_validation->set_rules('total_rate_split_dx_unit', lang('total-rate-split-dx-unit'), 'trim|required|numeric');

		//$this->form_validation->set_rules('chilled_water_system_total_rate', lang('chilled-water-system-total-rate'), 'trim|required|numeric');

		//$this->form_validation->set_rules('renewable_energy[renewable_energy_type][]', lang('renewable-energy-type'), 'trim|required|numeric');

		//$this->form_validation->set_rules('renewable_energy[renewable_energy_quantity][]', lang('renewable-energy-quantity'), 'trim|required|numeric');

		//$this->form_validation->set_rules('renewable_energy[renewable_energy_capacity][]', lang('renewable-energy-capacirty'), 'trim|required|numeric');

		$this->form_validation->set_rules('region_id', lang('region'), 'trim|required');

		$this->form_validation->set_rules('country_id', lang('country'), 'trim|required');
		// $this->form_validation->set_rules('hotel_id', lang('hotel'), 'trim|required');
		$this->form_validation->set_rules('site_type', lang('site-type'), 'trim|required');
		$this->form_validation->set_rules('attribute', lang('attribute'), 'trim|required');
		$this->form_validation->set_rules('residences_attribute', lang('residences-attribute'), 'trim');
		$this->form_validation->set_rules('rental_program_attribute', lang('rental-program-attribute'), 'trim');
		$this->form_validation->set_rules('employee_quarter_attribute', lang('employee-quarter-attribute'), 'trim');
		$this->form_validation->set_rules('baseline_regression_year', lang('baseline_regression_year'), 'trim|numeric');
		$this->form_validation->set_rules('energy_intensity_annual_target', lang('energy-intensity'), 'trim');
		$this->form_validation->set_rules('ghg_intensity_annual_target', lang('ghg-intensity'), 'trim');
		$this->form_validation->set_rules('water_intensity_annual_target', lang('water-intensity'), 'trim');
		$this->form_validation->set_rules('waste_intensity_annual_target', lang('waste-intensity'), 'trim');
		$this->form_validation->set_rules('energy_intensity_benchmark_target', lang('energy-intensity'), 'trim');
		$this->form_validation->set_rules('ghg_intensity_benchmark_target', lang('ghg-intensity'), 'trim');
		$this->form_validation->set_rules('water_intensity_benchmark_target', lang('water-intensity'), 'trim');
		$this->form_validation->set_rules('waste_intensity_benchmark_target', lang('waste-intensity'), 'trim');

		$utilities        = array('show_utility_electricity', 'show_utility_fuel_oil', 'show_utility_lpg', 'show_utility_water', 'show_utility_irrigation_water', 'show_utility_natural_gas', 'show_utility_district_cooling', 'show_utility_district_heating', 'show_waste_management', 'show_utility_fleet');

		$energy_modelling = [

			'show_utility_electricity'      => 'electricity',

			'show_utility_fuel_oil'         => 'fuel_oil',

			'show_utility_lpg'              => 'lpg',

			'show_utility_water'            => 'water',

			'show_utility_natural_gas'      => 'natural_gas',

			'show_utility_district_cooling' => 'district_cooling',

			'show_utility_district_heating' => 'district_heating',

		];

		foreach ($utilities as $utility) {

			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][cdd]', 'CDD', 'trim|numeric');

			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][hdd]', 'HDD', 'trim|numeric');

			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][occupancy]', 'Occupancy', 'trim|numeric');

			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][x]', 'X', 'trim|numeric');

			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][days]', 'Days', 'trim|numeric');
			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][r2]', 'R2', 'trim|numeric');
			$this->form_validation->set_rules('energy_modeling[' . $energy_modelling[$utility] . '][report]', 'Report', 'trim|numeric');
		}
	}



	public function valid_upload()

	{

		if (!empty($_FILES["site_logo"]["tmp_name"])) {

			$check = getimagesize($_FILES["site_logo"]["tmp_name"]);

			if ($check !== false) {

				$target_dir    = "uploads/";

				$target_file   = $target_dir . basename($_FILES["site_logo"]["name"]);

				$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);



				if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {

					$this->form_validation->set_message('valid_upload', "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");

					return false;
				} else {

					// Image size validation

					if ($_FILES["site_logo"]["size"] > 10485760) {

						$this->form_validation->set_message('valid_upload', 'Sorry, your file is too large. Maximum image size shold be < 10MB');

						return false;
					} else {

						return true;
					}
				}
			} else {

				$this->form_validation->set_message('valid_upload', "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");

				return false;
			}
		} else {

			$this->form_validation->set_message('valid_upload', "The site logo is required.");

			return false;
		}
	}



	public function float_check($value)

	{

		if ((!preg_match('/^[0-9]{1,6}+(\.[0-9]{1,4})?$/', $value)) || (!preg_match('/^[0-9]{1,4}+(\.[0-9]{1,4})?$/', $value))) {

			$this->form_validation->set_message('float_check', 'please Enter valid value');

			return false;
		} else {

			return true;
		}
	}

	public function add($id = 0)
	{

		$id = intval($id);

		if (is_uploaded_file($_FILES['importfile']['tmp_name'])) {

			require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';

			$file_tmp  = $_FILES['importfile']['tmp_name'];

			$file_name = $_FILES['importfile']['name'];

			$fileType  = pathinfo($file_name, PATHINFO_EXTENSION);

			if ($fileType == "") {

				$this->theme->set_message("Please upload file type with .xls extension.", 'error');

				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);

				exit;
			} else if ($fileType != "xls") {

				$this->theme->set_message("File type with .xls extension is allowed.", 'error');

				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);

				exit;
			} else {

				$this->load->model('utilities/utilities_model');



				$data = new Spreadsheet_Excel_Reader($file_tmp, false);



				$fieldNamesArray = array(

					'site_id',

					'measure_id',

					'low',

					'lower_quartile',

					'mean',

					'median',

					'upper_quartile',

					'high',

					'sd'

				);

				$numberRow = $data->sheets[0]['numRows'];

				$numberCol = $data->sheets[0]['numCols'];



				/* Number Of columns define */

				$colmuns['Site Name']         = "site_id";

				$colmuns['Measure']           = "measure_id";

				$colmuns['Low']               = "low";

				$colmuns['Lower Quartile']    = "lower_quartile";

				$colmuns['Mean']              = "mean";

				$colmuns['Median']            = "median";

				$colmuns['Upper Quartile']    = "upper_quartile";

				$colmuns['High']              = "high";

				$colmuns['SD']                = "sd";



				/* Number Of columns define */

				$sites_name   = array();

				$k            = 0;

				$totalCol     = 0;

				for ($i = 1; $i < 2; $i++) {

					for ($j = 1; $j <= $numberCol; $j++) {

						if ($data->sheets[0]['cells'][1][$j] != '') {

							$totalCol++;
						}
					}
				}



				// Get all site id for imported data

				$siteNames = array();

				for ($i = 2; $i <= $numberRow; $i++) {

					$siteNames[] = trim($data->sheets[0]['cells'][$i][1]);
				}

				$siteNames  = array_values(array_unique($siteNames));

				$allSiteids = $this->import_model->get_site_detail_by_name($siteNames);



				/* Start Of Number of rows*/

				for ($i = 2; $i <= $numberRow; $i++) {

					$statusInsert        = 0;

					$statusInsertUtility = 0;

					$extraData           = array();

					$dataInsertTotal     = array();

					$dataInsert          = array();



					if ($data->sheets[0]['cells'][$i][1] == '') {

						//$this->theme->set_message("Site do not Exists.", 'error');

						continue;
					}



					$siteId = $allSiteids[trim($data->sheets[0]['cells'][$i][1])]['id'];



					$measure_name = trim($data->sheets[0]['cells'][$i][2]);

					$measure_id = $this->import_model->get_measureId($measure_name);



					if ($siteId == '') {

						$sites_name[] = $data->sheets[0]['cells'][$i][1];

						continue;
					} else {

						$dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;

						$dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][1])]]      = $siteId;
					}



					for ($j = 2; $j <= $totalCol; $j++) {

						if (trim($data->sheets[0]['cells'][$i][$j]) != '') {

							$dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][$j])]] = $data->sheets[0]['cells'][$i][$j];
						} else {

							continue;
						}
					}



					$dataInsert['measure_id'] = $measure_id[0]['measures']['id'];



					$n = 1;

					foreach ($dataInsert as $key => $value) {

						if (in_array($key, $fieldNamesArray)) {

							$n++;
						}
					}



					if ($n == 10) {

						$this->import_model->delete_measure_entry_ifexist($dataInsert);

						$statusInsertUtility = $this->import_model->insert_site_measures_reading($dataInsert);

						unset($dataInsert);
					} else {

						//continue;

						$this->theme->set_message("Please import excelsheet with proper data", 'error');

						redirect(BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);
					}

					$k++;



					if ($statusInsertUtility) {

						$this->theme->set_message("File imported successfully.", 'success');

						unlink($target_file);
					}
				}

				/* End Of Number of rows*/
			}

			/*

	    //Create page-title

	    $this->theme->set('page_title', lang('sites'));

	    if (!empty($sites_name)) {

		$site_names = implode(',', $sites_name);

		$this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');

	    }

	    //Render view

	    redirect(BASE_ADMIN_URL_CUSTOM . 'sites');*/
		}



		$user_id = $this->session->userdata[$this->section_name]['user_id'];

		$site_id = $this->session->userdata[$this->section_name]['site_id'];

		$role_id = $this->session->userdata[$this->section_name]['role_id'];



		//filter array to get energy modelling data

		$filterArray = [

			'site_id' => $id,

			'year'    => date('Y'),

		];



		$this->sites_model->site_id = $this->site_id;



		if ($this->input->post('mysubmit')) {

			$data = $this->input->post();

			$id   = intval($data['id']);



			$sitedetail = $this->sites_model->get_site_detail($id, $user_id, $role_id);

			if (!empty($id) && isset($id) && !empty($data['siteArea']) && isset($data['siteArea']) && $data['siteArea']['area_update_type'] == 1) {
				$dataFetch['site_id'] = $id;
				$dataFetch['area_update_field'] = $data['siteArea']['area_update_field'];
				$latestAreaEntry = $this->sites_model->getlatestSiteArea($dataFetch);
				if (isset($latestAreaEntry) && !empty($latestAreaEntry)) {
					$latestAreaEntry = (array) $latestAreaEntry;
					$oldValue = $latestAreaEntry['area_update_value'];
				} else {
					$oldValue = $sitedetail[$data['siteArea']['area_update_field']];
				}
				if ($oldValue != $data['siteArea']['area_update_value'] && !empty($data['siteArea']['area_update_value']) && isset($data['siteArea']['area_update_value'])) {
					$date = date_create($data['siteArea']['area_update_date']);
					$dataSiteArea['site_id'] = $id;
					$dataSiteArea['area_update_field'] = $data['siteArea']['area_update_field'];
					$dataSiteArea['area_update_date'] = date_format($date, "Y-m-d");
					$dataSiteArea['area_old_value'] = isset($oldValue) && !empty($oldValue) ? $oldValue : 0;
					$dataSiteArea['area_update_value'] = $data['siteArea']['area_update_value'];
					$dataSiteArea['created_by'] = $user_id;
					$dataSiteArea['created_at'] = GetCurrentDateTime();
					$this->sites_model->updateSiteAreas($dataSiteArea, $site_location_name);

					// START - Update Residence Float area percentage with respect to new built_up_area
					if (($dataSiteArea['area_update_field'] == 'site_builtup_area') && isset($sitedetail['residence_types'])) {
						$residenceTypes = explode(',', $sitedetail['residence_types']);
						$this->load->model('sites/site_residence_model');
						foreach ($residenceTypes as $key => $value) {
							if (in_array($value, [RENTAL_PROGRAM_RESIDENCE, PRIVATE_RESIDENCE])) {
								$this->site_residence_model->site_id = $id;
								$this->site_residence_model->year_id = NULL;
								$this->site_residence_model->utility_type = NULL;
								$dataFloatPercent = [];
								$dataFloatPercent['site_builtup_area'] = $dataSiteArea['area_update_value'];
								if (($value == RENTAL_PROGRAM_RESIDENCE) && isset($sitedetail['rental_program_residence']) && !empty($sitedetail['rental_program_residence'])) {
									$this->site_residence_model->rental_program_residence_consumption = 2;
									$this->site_residence_model->private_program_consumption = NULL;
									$siteWithFloatRentalConsumptions = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
									if (isset($siteWithFloatRentalConsumptions) && !empty($siteWithFloatRentalConsumptions)) {
										$dataFloatPercent['rental_program_residence'] = $sitedetail['rental_program_residence'];
										$updatedPercentage = $this->site_residence_model->calculateFloatPercentage($dataFloatPercent, RENTAL_PROGRAM_RESIDENCE);
										$this->site_residence_model->updatePercentage($updatedPercentage, 'rental_program_residence_float', 'rental_program_residence_consumption');
									}
								} else if (($value == PRIVATE_RESIDENCE) && isset($sitedetail['rental_private_residence']) && !empty($sitedetail['rental_private_residence'])) {
									$this->site_residence_model->rental_program_residence_consumption = NULL;
									$this->site_residence_model->private_program_consumption = 2;
									$siteWithFloatPrivateConsumptions = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
									if (isset($siteWithFloatPrivateConsumptions) && !empty($siteWithFloatPrivateConsumptions)) {
										$dataFloatPercent['rental_private_residence'] = $sitedetail['rental_private_residence'];
										$updatedPercentage = $this->site_residence_model->calculateFloatPercentage($dataFloatPercent, PRIVATE_RESIDENCE);
										$this->site_residence_model->updatePercentage($updatedPercentage, 'private_program_float', 'private_program_consumption');
									}
								}
							}
						}
					}
					// END - Update Residence Float area percentage with respect to new built_up_area
				}
			} else if (empty($id) && !isset($id) && !empty($data['siteArea']) && isset($data['siteArea']) && $data['siteArea']['area_update_type'] == 1) {
				$data[$data['siteArea']['area_update_field']] = $data['siteArea']['area_update_value'];
			}

			if (empty($sitedetail['site_logo']) || !empty($_FILES["site_logo"]["tmp_name"])) {
				$this->form_validation->set_rules('site_logo', lang('hotel-logo'), 'callback_valid_upload');
			}
			$hotel_name              = trim(strip_tags($data['hotel_name']));

			$site_location_name      = trim(strip_tags($data['site_location_name']));
			$residence_types         = isset($data['residence_types']) ? implode(',', $data['residence_types']) : NULL;
			$city  = $data['city'];
			$site_location_latitude  = $data['site_location_latitude'];

			$site_location_longitude = $data['site_location_longitude'];

			$station_id              = $data['station_id'];

			$base_cdd_temprature     = $data['base_cdd_temprature'];

			$base_hdd_temprature     = $data['base_hdd_temprature'];

			$region_id               = intval($data['region_id']);

			$country_id              = intval($data['country_id']);
			$hotel_id                = intval(1); //intval($data['hotel_id']);
			$site_type               = intval($data['site_type']);
			$attribute               = $data['attribute'];
			$residences_attribute    = $data['residences_attribute'];
			$rental_program_attribute = $data['rental_program_attribute'];
			$employee_quarter_attribute                    = $data['employee_quarter_attribute'];
			$site_year_built         = intval($data['site_year_built']);

			$site_builtup_area       = isset($data['site_builtup_area']) ? $data['site_builtup_area'] : 0;

			$cooled_builtup_area     = isset($data['cooled_builtup_area']) ? $data['cooled_builtup_area'] : 0;

			$total_meeting_area      = $data['total_meeting_area'];

			$total_spa_area          = $data['total_spa_area'];
			$room_area_rental_program          = $data['room_area_rental_program'];
			$room_area_private_residence          = $data['room_area_private_residence'];
			$hotel_rooms_area          = isset($data['hotel_rooms_area']) ? $data['hotel_rooms_area'] : 0;
			$residential_common_area          = $data['residential_common_area'];
			$employee_living_quarters_area          = $data['employee_living_quarters_area'];
			$f_b_service          = $data['f_b_service'];
			$restaurant_area          = $data['restaurant_area'];
			$landscaped_area          = $data['landscaped_area'];
			$comments          = $data['comments'];
			$f_b_services_operated          = $data['f_b_services_operated'];
			$f_b_services_outsourced          = $data['f_b_services_outsourced'];
			$month_year_operation          = $data['month_year_operation'];
			$vehicle_electric          = $data['vehicle_electric'];
			$vehicle_petrol          = $data['vehicle_petrol'];
			$rental_program_residence          = $data['rental_program_residence'];
			$rental_private_residence          = $data['rental_private_residence'];
			$rental_program_residence_conditioned          = $data['rental_program_residence_conditioned'];
			$rental_private_residence_conditioned          = $data['rental_private_residence_conditioned'];
			$rental_program_residence_suites          = $data['rental_program_residence_suites'];
			$rental_private_residence_suites          = $data['rental_private_residence_suites'];
			$total_guest_room_area   = $data['total_guest_room_area'];
			$indoor_parking_area     = $data['indoor_parking_area'];

			$rooms_keys              = intval($data['rooms_keys']);

			$outdoor_pools           = intval($data['outdoor_pools']);

			$indoor_pools            = intval($data['indoor_pools']);

			$laundry_type            = intval($data['laundry_type']);



			$show_utility_electricity      = intval($data['show_utility_electricity']);

			$show_utility_fuel_oil         = intval($data['show_utility_fuel_oil']);

			$show_utility_lpg              = intval($data['show_utility_lpg']);

			$show_utility_water            = intval($data['show_utility_water']);

			$show_utility_irrigation_water = intval($data['show_utility_irrigation_water']);

			$show_utility_natural_gas      = intval($data['show_utility_natural_gas']);

			$show_utility_water_waste      = intval($data['show_utility_water_waste']);

			$show_utility_district_cooling = intval($data['show_utility_district_cooling']);

			$show_utility_district_heating = intval($data['show_utility_district_heating']);
			$show_utility_district_heating_boiler = intval($data['show_utility_district_heating_boiler']);
			$show_waste_management         = intval($data['show_waste_management']);
			$show_utility_fleet         = intval($data['show_utility_fleet']);

			$utility_unit_electricity      = intval($data['utility_unit_electricity']);
			$utility_unit_fuel_oil         = intval($data['utility_unit_fuel_oil']);
			$utility_unit_lpg              = intval($data['utility_unit_lpg']);
			$utility_unit_water            = intval($data['utility_unit_water']);
			$utility_unit_natural_gas      = intval($data['utility_unit_natural_gas']);
			$utility_unit_district_cooling = intval($data['utility_unit_district_cooling']);
			$utility_unit_district_heating = intval($data['utility_unit_district_heating']);

			$chsb_reporting                = intval($data['chsb_reporting']);
			$chsb_segment                = intval($data['chsb_segment']);
			$csr                           = intval($data['csr']);

			$daily_metering                = intval($data['daily_metering']);

			$is_hourly                     = intval($data['is_hourly']);

			$energy_intensity_annual_target                     = $data['energy_intensity_annual_target'];
			$ghg_intensity_annual_target                     = $data['ghg_intensity_annual_target'];
			$water_intensity_annual_target                     = $data['water_intensity_annual_target'];
			$waste_intensity_annual_target                     = $data['waste_intensity_annual_target'];
			$energy_intensity_benchmark_target                     = $data['energy_intensity_benchmark_target'];
			$ghg_intensity_benchmark_target                     = $data['ghg_intensity_benchmark_target'];
			$water_intensity_benchmark_target                     = $data['water_intensity_benchmark_target'];
			$waste_intensity_benchmark_target                     = $data['waste_intensity_benchmark_target'];

			$show_total_utility_notification = intval($data['show_total_utility_notification']);



			$laundry_fuel_type       = trim(strip_tags($data['laundry_fuel_type']));

			$is_chilled_water_system = intval($data['is_chilled_water_system']);

			$is_used_in_cron = intval($data['is_used_in_cron']);

			$threshold = $data['threshold'];

			if ($is_chilled_water_system == 1) {

				$chilled_water_system_type       = trim(strip_tags($data['chilled_water_system_type']));

				$chilled_water_system_total_rate = $data['chilled_water_system_total_rate'];
			} else {

				$chilled_water_system_type       = '';

				$chilled_water_system_total_rate = '';
			}



			$is_split_dx_unit = intval($data['is_split_dx_unit']);

			if ($is_split_dx_unit == 1) {

				$total_split_dx_unit      = intval($data['total_split_dx_unit']);

				$total_rate_split_dx_unit = $data['total_rate_split_dx_unit'];
			} else {

				$total_split_dx_unit      = '';

				$total_rate_split_dx_unit = '';
			}



			$is_vrv = intval($data['is_vrv']);

			if ($is_vrv == 1) {

				$total_vrv      = intval($data['total_vrv']);

				$total_vrv_unit = $data['total_vrv_unit'];
			} else {

				$total_vrv      = '';

				$total_vrv_unit = '';
			}



			$calorifiers_unit   = $data['calorifiers_unit'];

			$calorifiers_volume = $data['calorifiers_volume'];



			$chilled_water_system_type2       = $data['chilled_water_system_type2'];

			$chilled_water_system_total_rate2 = $data['chilled_water_system_total_rate2'];



			$elcetrical_hw_total          = intval($data['elcetrical_hw_total']);

			$elcetrical_hw_total_capacity = $data['elcetrical_hw_total_capacity'];

			$elcetrical_hw_total_power    = $data['elcetrical_hw_total_power'];

			$is_ro_plant                  = intval($data['is_ro_plant']);

			if ($is_ro_plant == 1) {

				$ro_plant_capacity = $data['ro_plant_capacity'];
			} else {

				$ro_plant_capacity = '';
			}



			$is_renewable_energy = intval($data['is_renewable_energy']);

			if ($is_renewable_energy == 1) {

				$data['renewable_energy'] = $data['renewable_energy'];
			} else {

				$data['renewable_energy'] = array();
			}



			$is_stp = intval($data['is_stp']);

			if ($is_stp == 1) {

				$stp_capacity = $data['stp_capacity'];
			} else {

				$stp_capacity = '';
			}



			$site_color                       = trim(strip_tags($data['site_color']));

			$electricity_emission_factor      = $data['electricity_emission_factor'];

			$fuel_emission_factor             = $data['fuel_emission_factor'];

			$lpg_emission_factor              = $data['lpg_emission_factor'];

			$natural_gas_emission_factor      = $data['natural_gas_emission_factor'];

			$district_cooling_emission_factor = $data['district_cooling_emission_factor'];

			$district_heating_emission_factor = $data['district_heating_emission_factor'];

			if ($id == 0) {

				$status = $data['status'];
			} else {

				$status = $data['status'];
			}



			$this->sites_validation_rules();



			if ($this->form_validation->run($this)) {

				if (isset($_FILES['site_logo']['name'])) {

					$config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";

					$config['max_size']       = '2048';

					$config['maintain_ratio'] = true;

					$config['width']          = 140;

					$config['height']         = 100;



					$this->load->library('upload', $config);

					$this->upload->initialize($config);



					$valid_formats = array("jpg", "png");

					$imagename     = $_FILES['site_logo']['name'];



					$size = $_FILES['site_logo']['size'];

					$i    = strrpos($imagename, ".");

					if (!$i) {

						$ext = '';
					}

					$l              = strlen($imagename) - $i;

					$ext            = substr($imagename, $i + 1, $l);

					$site_logo_name = 'site_logo_' . rand(11111, 9999999) . '.' . $ext;

					if ($ext) {

						if (in_array($ext, $valid_formats)) {

							// procedure further if and only if image size can not be more than 10MB.

							if ($size < (1024 * 1024 * 10)) {

								$uploadedfile = $_FILES['site_logo']['tmp_name'];

								$target_file  = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_logo_name;

								$_movestatus  = move_uploaded_file($uploadedfile, $target_file);



								if (!$_movestatus) {

									$this->theme->set_message('site image is not uploaded', 'error');
								} else {

									$this->load->library('image_lib');

									$config['image_library'] = 'gd2';

									$config['source_image']  = $target_file;

									$this->image_lib->clear();

									$this->image_lib->initialize($config);



									if (!$this->image_lib->resize()) {

										echo $this->image_lib->display_errors();
									}



									$site_logo               = trim(strip_tags($site_logo_name));

									$data_array['site_logo'] = $site_logo;



									// Delete Old file

									$oldfile = BASE_PATH_CUSTOM . "/assets/uploads/" . $sitedetail['site_logo'];

									if (file_exists($oldfile)) {

										unlink($oldfile);
									}
								}
							} else {

								$this->theme->set_message('site image size is too large', 'error');
							}
						} else {

							$this->theme->set_message('site image extension is not .jpg or .png formate', 'error');
						}
					}
				}



				$data_array['id']                              = $id;

				$data_array['hotel_name']                      = $hotel_name;

				$data_array['site_location_name']              = $site_location_name;
				$data_array['residence_types']                  = $residence_types;
				$data_array['city']          = $city;
				$data_array['site_location_latitude']          = $site_location_latitude;

				$data_array['site_location_longitude']         = $site_location_longitude;

				$data_array['station_id']                      = $station_id;

				$data_array['base_cdd_temprature']             = $base_cdd_temprature;

				$data_array['base_hdd_temprature']             = $base_hdd_temprature;

				$data_array['region_id']                       = $region_id;

				$data_array['country_id']                      = $country_id;

				$data_array['hotel_id']                        = $hotel_id;

				$data_array['site_type']                       = $site_type;
				$data_array['attribute']                       = $attribute;
				$data_array['residences_attribute']            = $residences_attribute;
				$data_array['rental_program_attribute']        = $rental_program_attribute;
				$data_array['employee_quarter_attribute']        = $employee_quarter_attribute;
				$data_array['site_year_built']                 = $site_year_built;

				$data_array['site_builtup_area']               = $site_builtup_area;

				$data_array['cooled_builtup_area']             = $cooled_builtup_area;

				$data_array['total_meeting_area']              = $total_meeting_area;

				$data_array['total_spa_area']                  = $total_spa_area;
				$data_array['room_area_rental_program']                  = $room_area_rental_program;
				$data_array['room_area_private_residence']                  = $room_area_private_residence;
				$data_array['hotel_rooms_area']                  = $hotel_rooms_area;
				$data_array['residential_common_area']                  = $residential_common_area;
				$data_array['employee_living_quarters_area']                  = $employee_living_quarters_area;
				$data_array['f_b_service']                  = $f_b_service;
				$data_array['restaurant_area']                  = $restaurant_area;
				$data_array['landscaped_area']                  = $landscaped_area;
				$data_array['comments']                  = $comments;
				$data_array['f_b_services_operated']                  = $f_b_services_operated;
				$data_array['f_b_services_outsourced']                  = $f_b_services_outsourced;
				$data_array['month_year_operation']                  = $month_year_operation;
				$data_array['vehicle_electric']                  = $vehicle_electric;
				$data_array['vehicle_petrol']                  = $vehicle_petrol;
				$data_array['rental_program_residence']                  = $rental_program_residence;
				$data_array['rental_private_residence']                  = $rental_private_residence;
				$data_array['rental_program_residence_conditioned']                  = $rental_program_residence_conditioned;
				$data_array['rental_private_residence_conditioned']                  = $rental_private_residence_conditioned;
				$data_array['rental_program_residence_suites']                  = $rental_program_residence_suites;
				$data_array['rental_private_residence_suites']                  = $rental_private_residence_suites;
				$data_array['total_guest_room_area']           = $total_guest_room_area;
				$data_array['indoor_parking_area']             = $indoor_parking_area;

				$data_array['rooms_keys']                      = $rooms_keys;

				$data_array['outdoor_pools']                   = $outdoor_pools;

				$data_array['indoor_pools']                    = $indoor_pools;

				$data_array['laundry_type']                    = $laundry_type;

				$data_array['laundry_fuel_type']               = $laundry_fuel_type;

				$data_array['is_chilled_water_system']         = $is_chilled_water_system;

				$data_array['is_used_in_cron']         = $is_used_in_cron;

				$data_array['threshold']         = $threshold;

				$data_array['chilled_water_system_type']       = $chilled_water_system_type;

				$data_array['chilled_water_system_total_rate'] = $chilled_water_system_total_rate;

				$data_array['is_split_dx_unit']                = $is_split_dx_unit;

				$data_array['total_split_dx_unit']             = $total_split_dx_unit;

				$data_array['total_rate_split_dx_unit']        = $total_rate_split_dx_unit;

				$data_array['is_vrv']                          = $is_vrv;

				$data_array['total_vrv']                       = $total_vrv;

				$data_array['total_vrv_unit']                  = $total_vrv_unit;



				$data_array['calorifiers_unit']                 = $calorifiers_unit;

				$data_array['calorifiers_volume']               = $calorifiers_volume;

				$data_array['chilled_water_system_type2']       = $chilled_water_system_type2;

				$data_array['chilled_water_system_total_rate2'] = $chilled_water_system_total_rate2;



				$data_array['elcetrical_hw_total']              = $elcetrical_hw_total;

				$data_array['elcetrical_hw_total_capacity']     = $elcetrical_hw_total_capacity;

				$data_array['elcetrical_hw_total_power']        = $elcetrical_hw_total_power;

				$data_array['is_ro_plant']                      = $is_ro_plant;

				$data_array['ro_plant_capacity']                = $ro_plant_capacity;

				$data_array['is_renewable_energy']              = $is_renewable_energy;

				$data_array['is_stp']                           = $is_stp;

				$data_array['stp_capacity']                     = $stp_capacity;

				$data_array['site_color']                       = $site_color;

				$data_array['electricity_emission_factor']      = $electricity_emission_factor;

				$data_array['fuel_emission_factor']             = $fuel_emission_factor;

				$data_array['lpg_emission_factor']              = $lpg_emission_factor;

				$data_array['natural_gas_emission_factor']      = $natural_gas_emission_factor;

				$data_array['district_cooling_emission_factor'] = $district_cooling_emission_factor;

				$data_array['district_heating_emission_factor'] = $district_heating_emission_factor;



				$data_array['show_utility_electricity']        = $show_utility_electricity;

				$data_array['show_utility_fuel_oil']           = $show_utility_fuel_oil;

				$data_array['show_utility_lpg']                = $show_utility_lpg;

				$data_array['show_utility_water']              = $show_utility_water;

				$data_array['show_utility_irrigation_water']   = $show_utility_irrigation_water;

				$data_array['show_utility_natural_gas']        = $show_utility_natural_gas;

				$data_array['show_utility_water_waste']        = $show_utility_water_waste;

				$data_array['show_utility_district_cooling']   = $show_utility_district_cooling;

				$data_array['show_utility_district_heating']   = $show_utility_district_heating;
				$data_array['show_utility_district_heating_boiler']   = $show_utility_district_heating_boiler;

				$data_array['utility_unit_electricity']        = $utility_unit_electricity;
				$data_array['utility_unit_fuel_oil']           = $utility_unit_fuel_oil;
				$data_array['utility_unit_lpg']                = $utility_unit_lpg;
				$data_array['utility_unit_water']              = $utility_unit_water;
				$data_array['utility_unit_natural_gas']        = $utility_unit_natural_gas;
				$data_array['utility_unit_district_cooling']   = $utility_unit_district_cooling;
				$data_array['utility_unit_district_heating']   = $utility_unit_district_heating;

				$data_array['show_waste_management']           = $show_waste_management;
				$data_array['show_utility_fleet']           = $show_utility_fleet;

				$data_array['baseline_regression_year']        = trim($data['baseline_regression_year']);

				$data_array['local_currency']                  = trim($data['local_currency']);
				$data_array['local_unit']                  = trim($data['local_unit']);
				$data_array['show_total_utility_notification'] = $show_total_utility_notification;

				$data_array['chsb_reporting']                  = $chsb_reporting;
				$data_array['chsb_segment']                  = $chsb_segment;
				$data_array['csr']                             = $csr;

				$data_array['daily_metering']                  = $daily_metering;

				$data_array['is_hourly']                       = $is_hourly;

				$data_array['energy_intensity_annual_target']                       = $energy_intensity_annual_target;
				$data_array['ghg_intensity_annual_target']                       = $ghg_intensity_annual_target;
				$data_array['water_intensity_annual_target']                       = $water_intensity_annual_target;
				$data_array['waste_intensity_annual_target']                       = $waste_intensity_annual_target;
				$data_array['energy_intensity_benchmark_target']                       = $energy_intensity_benchmark_target;
				$data_array['ghg_intensity_benchmark_target']                       = $ghg_intensity_benchmark_target;
				$data_array['water_intensity_benchmark_target']                       = $water_intensity_benchmark_target;
				$data_array['waste_intensity_benchmark_target']                       = $waste_intensity_benchmark_target;

				$data_array['status']  = $status;

				$data_array['user_id'] = $this->session->userdata[$this->section_name]['user_id'];



				$site_id = $this->sites_model->save_site($data_array);

				//set changed local currency to session

				$this->session->set_custom_userdata($this->section_name, "local_currency", $data_array['local_currency']);
				//set changed local unit to session
				$this->session->set_custom_userdata($this->section_name, "local_unit", $data_array['local_unit']);
				//data to pass for saving energy modeling data

				$energy_data = $data['energy_modeling'];

				foreach ($energy_data as $key => $energy) {

					$energy['year_id'] = date('Y');

					$energy['site_id'] = $site_id;

					$energy_data[$key] = $energy;
				}

				$this->sites_model->save_energy_modelling($energy_data);



				$this->sites_model->save_substation($site_id, $data['substation']);

				$this->sites_model->save_generator($site_id, $data['generator']);

				$this->sites_model->save_hot_water_boiler($site_id, $data['hot_water_boiler']);

				$this->sites_model->save_steam_boiler($site_id, $data['steam_boiler']);

				$this->sites_model->save_renewable_energy($site_id, $data['renewable_energy'], $is_renewable_energy);

				$this->sites_model->save_daily_reading_settings($site_id, $data['daily_reading'], $data['daily_reading']);

				$this->sites_model->save_daily_reading_utilites_setting($site_id, $data['daily_reading_utlities']);



				if (empty($data_array['id'])) {
					$site_notification_lists = getNotificationStaticList($site_id);
					$this->sites_model->save_default_notifications($site_id, $site_notification_lists);
				}
				$this->sites_model->assign_site_to_corporate_and_super_admins($site_id, isset($data_array['region_id']) ? $data_array['region_id'] : 0);



				if ($id == 0) {

					$this->theme->set_message(lang('site-add-success'), 'success');
				} else {

					$this->theme->set_message(lang('site-edit-success'), 'success');
				}



				// redirect(BASE_ADMIN_URL_CUSTOM . 'sites');

			}
		}



		$result = $this->sites_model->get_site_detail($id, $user_id, $role_id);


		$data['site_id'] = 0;

		if (!empty($result)) {



			$data['site_id'] = $result['id'];

			//Variable assignment for edit view

			$hotel_name                       = $result['hotel_name'];

			$site_location_name               = $result['site_location_name'];
			$residence_types                  = isset($result['residence_types']) ? explode(',', $result['residence_types']) : [];
			$city           = $result['city'];
			$site_location_latitude           = $result['site_location_latitude'];

			$site_location_longitude          = $result['site_location_longitude'];

			$station_id                       = $result['station_id'];

			$base_cdd_temprature              = $result['base_cdd_temprature'];

			$base_hdd_temprature              = $result['base_hdd_temprature'];

			$region_id                        = $result['region_id'];

			$country_id                       = $result['country_id'];

			$hotel_id                         = $result['hotel_id'];

			$site_type                        = $result['site_type'];
			$attribute                        = $result['attribute'];
			$residences_attribute             = $result['residences_attribute'];
			$rental_program_attribute         = $result['rental_program_attribute'];
			$employee_quarter_attribute         = $result['employee_quarter_attribute'];
			$site_year_built                  = $result['site_year_built'];

			$site_builtup_area                = $result['site_builtup_area'];

			$cooled_builtup_area              = $result['cooled_builtup_area'];

			$total_meeting_area               = $result['total_meeting_area'];

			$total_spa_area                   = $result['total_spa_area'];
			$room_area_rental_program                   = $result['room_area_rental_program'];
			$room_area_private_residence                   = $result['room_area_private_residence'];
			$hotel_rooms_area                   = $result['hotel_rooms_area'];
			$residential_common_area                   = $result['residential_common_area'];
			$employee_living_quarters_area                   = $result['employee_living_quarters_area'];
			$f_b_service                   = $result['f_b_service'];
			$restaurant_area                   = $result['restaurant_area'];
			$landscaped_area                   = $result['landscaped_area'];
			$comments                   = $result['comments'];
			$f_b_services_operated                   = $result['f_b_services_operated'];
			$f_b_services_outsourced                   = $result['f_b_services_outsourced'];
			$month_year_operation                   = $result['month_year_operation'];
			$vehicle_electric                   = $result['vehicle_electric'];
			$vehicle_petrol                   = $result['vehicle_petrol'];
			$rental_program_residence                   = $result['rental_program_residence'];
			$rental_private_residence                   = $result['rental_private_residence'];
			$rental_program_residence_conditioned                   = $result['rental_program_residence_conditioned'];
			$rental_private_residence_conditioned                   = $result['rental_private_residence_conditioned'];
			$rental_program_residence_suites                   = $result['rental_program_residence_suites'];
			$rental_private_residence_suites                   = $result['rental_private_residence_suites'];
			$total_guest_room_area            = $result['total_guest_room_area'];
			$indoor_parking_area              = $result['indoor_parking_area'];

			$rooms_keys                       = $result['rooms_keys'];

			$outdoor_pools                    = $result['outdoor_pools'];

			$indoor_pools                     = $result['indoor_pools'];

			$laundry_type                     = isset($result['laundry_type']) ? $result['laundry_type'] : 1;

			$laundry_fuel_type                = $result['laundry_fuel_type'];

			$is_chilled_water_system          = isset($result['is_chilled_water_system']) ? $result['is_chilled_water_system'] : 1;

			$chilled_water_system_type        = $result['chilled_water_system_type'];

			$chilled_water_system_total_rate  = $result['chilled_water_system_total_rate'];

			$is_split_dx_unit                 = isset($result['is_split_dx_unit']) ? $result['is_split_dx_unit'] : 1;

			$total_split_dx_unit              = $result['total_split_dx_unit'];

			$total_rate_split_dx_unit         = $result['total_rate_split_dx_unit'];

			$is_vrv                           = isset($result['is_vrv']) ? $result['is_vrv'] : 1;

			$total_vrv                        = $result['total_vrv'];

			$total_vrv_unit                   = $result['total_vrv_unit'];

			$calorifiers_unit                 = $result['calorifiers_unit'];

			$calorifiers_volume               = $result['calorifiers_volume'];

			$chilled_water_system_type2       = $result['chilled_water_system_type2'];

			$chilled_water_system_total_rate2 = $result['chilled_water_system_total_rate2'];

			$elcetrical_hw_total              = $result['elcetrical_hw_total'];

			$elcetrical_hw_total_capacity     = $result['elcetrical_hw_total_capacity'];

			$elcetrical_hw_total_power        = $result['elcetrical_hw_total_power'];

			$is_ro_plant                      = isset($result['is_ro_plant']) ? $result['is_ro_plant'] : 1;

			$ro_plant_capacity                = $result['ro_plant_capacity'];

			$is_renewable_energy              = isset($result['is_renewable_energy']) ? $result['is_renewable_energy'] : 1;

			$is_stp                           = isset($result['is_stp']) ? $result['is_stp'] : 1;

			$stp_capacity                     = $result['stp_capacity'];

			$site_logo                        = $result['site_logo'];

			$site_color                       = $result['site_color'];

			$electricity_emission_factor      = $result['electricity_emission_factor'];

			$fuel_emission_factor             = $result['fuel_emission_factor'];

			$lpg_emission_factor              = $result['lpg_emission_factor'];

			$natural_gas_emission_factor      = $result['natural_gas_emission_factor'];

			$district_cooling_emission_factor = $result['district_cooling_emission_factor'];

			$district_heating_emission_factor = $result['district_heating_emission_factor'];

			$status                           = isset($result['status']) ? $result['status'] : 1;

			$substations                      = $this->sites_model->get_substations($data['site_id']);

			$data['substations']              = $substations;

			$generators                       = $this->sites_model->get_generators($data['site_id']);

			$data['generators']               = $generators;

			$hot_water_boilers                = $this->sites_model->get_hot_water_boilers($data['site_id']);

			$data['hot_water_boilers']        = $hot_water_boilers;

			$steam_boilers                    = $this->sites_model->get_steam_boilers($data['site_id']);

			$data['steam_boilers']            = $steam_boilers;

			$renewable_energys                = $this->sites_model->get_renewable_energys($data['site_id']);

			$data['renewable_energys']        = $renewable_energys;



			$daily_reading_settings         = $this->sites_model->get_daily_reading_settings($data['site_id']);

			$data['daily_reading_settings'] = $daily_reading_settings;



			$show_utility_electricity      = $result['show_utility_electricity'];

			$show_utility_fuel_oil         = $result['show_utility_fuel_oil'];

			$show_utility_lpg              = $result['show_utility_lpg'];

			$show_utility_water            = $result['show_utility_water'];

			$show_utility_irrigation_water = $result['show_utility_irrigation_water'];

			$show_utility_natural_gas      = $result['show_utility_natural_gas'];

			$show_utility_water_waste      = $result['show_utility_water_waste'];

			$show_utility_district_cooling = $result['show_utility_district_cooling'];

			$show_utility_district_heating = $result['show_utility_district_heating'];
			$show_utility_district_heating_boiler = $result['show_utility_district_heating_boiler'];
			$show_waste_management         = $result['show_waste_management'];
			$show_utility_fleet         = $result['show_utility_fleet'];

			$chsb_reporting          = $result['chsb_reporting'];
			$chsb_segment          = $result['chsb_segment'];
			$csr                     = $result['csr'];

			$is_used_in_cron         = $result['is_used_in_cron'];

			$threshold               = $result['threshold'];

			$daily_metering          = $result['daily_metering'];

			$is_hourly               = $result['is_hourly'];
			$energy_intensity_annual_target               = $result['energy_intensity_annual_target'];
			$ghg_intensity_annual_target               = $result['ghg_intensity_annual_target'];
			$water_intensity_annual_target               = $result['water_intensity_annual_target'];
			$waste_intensity_annual_target               = $result['waste_intensity_annual_target'];
			$energy_intensity_benchmark_target               = $result['energy_intensity_benchmark_target'];
			$ghg_intensity_benchmark_target               = $result['ghg_intensity_benchmark_target'];
			$water_intensity_benchmark_target               = $result['water_intensity_benchmark_target'];
			$waste_intensity_benchmark_target               = $result['waste_intensity_benchmark_target'];

			$show_total_utility_notification = $result['show_total_utility_notification'];



			//get energy data and pass it to view data

			$energy_modelling_data            = $this->sites_model->get_energy_modelling($filterArray);

			$data['energy_modelling_data']    = $energy_modelling_data;

			$data['baseline_regression_year'] = $result['baseline_regression_year'];

			$data['local_currency']           = $result['local_currency'];
			$data['local_unit']           = $result['local_unit'];
		}



		$region_list                  = $this->sites_model->region_list();

		$hotel_list                   = $this->sites_model->hotel_list();

		$country_list                 = $this->sites_model->country_list();

		$daily_reading_utilities_list = $this->sites_model->daily_reading_utilities_list();

		$read_daily_reading_utilites_setting = $this->sites_model->read_daily_reading_utilites_setting($result['id']);

		$measure_readings         = $this->sites_model->get_measure_readings($id);

		$data['measure_readings'] = $measure_readings;
		$data['residence_types'] = $residence_types;


		// Pass data to view file

		$data['id']                               = $id;

		$data['hotel_name']                       = $hotel_name;

		$data['site_location_name']               = $site_location_name;

		$data['city']           = $city;
		$data['site_location_latitude']           = $site_location_latitude;

		$data['site_location_longitude']          = $site_location_longitude;

		$data['station_id']                       = $station_id;

		$data['base_cdd_temprature']              = $base_cdd_temprature;

		$data['base_hdd_temprature']              = $base_hdd_temprature;

		$data['region_id']                        = $region_id;

		$data['country_id']                       = $country_id;

		$data['hotel_list']                       = $hotel_list;

		$data['hotel_id']                         = $hotel_id;

		$data['site_type']                        = $site_type;
		$data['attribute']                        = $attribute;
		$data['residences_attribute']             = $residences_attribute;
		$data['rental_program_attribute']         = $rental_program_attribute;
		$data['employee_quarter_attribute']       = $employee_quarter_attribute;
		$data['region_list']                      = $region_list;

		$data['country_list']                     = $country_list;

		$data['site_year_built']                  = $site_year_built;

		$data['site_builtup_area']                = $site_builtup_area;

		$data['cooled_builtup_area']              = $cooled_builtup_area;

		$data['total_meeting_area']               = $total_meeting_area;

		$data['total_spa_area']                   = $total_spa_area;
		$data['room_area_rental_program']         = $room_area_rental_program;
		$data['room_area_private_residence']      = $room_area_private_residence;
		$data['hotel_rooms_area']                 = $hotel_rooms_area;
		$data['residential_common_area']          = $residential_common_area;
		$data['employee_living_quarters_area']    = $employee_living_quarters_area;
		$data['f_b_service']                      = $f_b_service;
		$data['restaurant_area']                  = $restaurant_area;
		$data['landscaped_area']                  = $landscaped_area;
		$data['comments']            = $comments;
		$data['f_b_services_operated']            = $f_b_services_operated;
		$data['f_b_services_outsourced']          = $f_b_services_outsourced;
		$data['month_year_operation']             = $month_year_operation;
		$data['vehicle_electric']                 = $vehicle_electric;
		$data['vehicle_petrol']                   = $vehicle_petrol;
		$data['rental_program_residence']                   = $rental_program_residence;
		$data['rental_private_residence']                   = $rental_private_residence;
		$data['rental_program_residence_conditioned']                   = $rental_program_residence_conditioned;
		$data['rental_private_residence_conditioned']                   = $rental_private_residence_conditioned;
		$data['rental_program_residence_suites']                   = $rental_program_residence_suites;
		$data['rental_private_residence_suites']                   = $rental_private_residence_suites;
		$data['total_guest_room_area']            = $total_guest_room_area;
		$data['indoor_parking_area']              = $indoor_parking_area;

		$data['rooms_keys']                       = $rooms_keys;

		$data['outdoor_pools']                    = $outdoor_pools;

		$data['indoor_pools']                     = $indoor_pools;

		$data['laundry_type']                     = $laundry_type;

		$data['laundry_fuel_type']                = $laundry_fuel_type;

		$data['is_chilled_water_system']          = $is_chilled_water_system;

		$data['chilled_water_system_type']        = $chilled_water_system_type;

		$data['chilled_water_system_total_rate']  = $chilled_water_system_total_rate;

		$data['is_split_dx_unit']                 = $is_split_dx_unit;

		$data['total_split_dx_unit']              = $total_split_dx_unit;

		$data['total_rate_split_dx_unit']         = $total_rate_split_dx_unit;

		$data['is_vrv']                           = $is_vrv;

		$data['total_vrv']                        = $total_vrv;

		$data['total_vrv_unit']                   = $total_vrv_unit;

		$data['chilled_water_system_type2']       = $chilled_water_system_type2;

		$data['chilled_water_system_total_rate2'] = $chilled_water_system_total_rate2;

		$data['calorifiers_unit']                 = $calorifiers_unit;

		$data['calorifiers_volume']               = $calorifiers_volume;

		$data['elcetrical_hw_total']              = $elcetrical_hw_total;

		$data['elcetrical_hw_total_capacity']     = $elcetrical_hw_total_capacity;

		$data['elcetrical_hw_total_power']        = $elcetrical_hw_total_power;

		$data['is_ro_plant']                      = $is_ro_plant;

		$data['ro_plant_capacity']                = $ro_plant_capacity;

		$data['is_renewable_energy']              = $is_renewable_energy;

		$data['is_stp']                           = $is_stp;

		$data['stp_capacity']                     = $stp_capacity;

		$data['site_logo']                        = $site_logo;

		$data['site_color']                       = $site_color;

		$data['electricity_emission_factor']      = $electricity_emission_factor;

		$data['fuel_emission_factor']             = $fuel_emission_factor;

		$data['lpg_emission_factor']              = $lpg_emission_factor;

		$data['natural_gas_emission_factor']      = $natural_gas_emission_factor;

		$data['district_cooling_emission_factor'] = $district_cooling_emission_factor;

		$data['district_heating_emission_factor'] = $district_heating_emission_factor;



		$data['show_utility_electricity']      = $show_utility_electricity;

		$data['show_utility_fuel_oil']         = $show_utility_fuel_oil;

		$data['show_utility_lpg']              = $show_utility_lpg;

		$data['show_utility_water']            = $show_utility_water;

		$data['show_utility_irrigation_water'] = $show_utility_irrigation_water;

		$data['show_utility_natural_gas']      = $show_utility_natural_gas;

		$data['show_utility_water_waste']      = $show_utility_water_waste;

		$data['show_utility_district_cooling'] = $show_utility_district_cooling;

		$data['show_utility_district_heating'] = $show_utility_district_heating;
		$data['show_utility_district_heating_boiler'] = $show_utility_district_heating_boiler;
		$data['show_waste_management']         = $show_waste_management;
		$data['show_utility_fleet']           = $show_utility_fleet;

		$data['show_total_utility_notification'] = $show_total_utility_notification;

		$data['chsb_reporting'] = $chsb_reporting;
		$data['chsb_segment'] = $chsb_segment;
		$data['csr']            = $csr;

		$data['daily_metering'] = $daily_metering;

		$data['is_hourly']      = $is_hourly;

		$data['energy_intensity_annual_target']      = $energy_intensity_annual_target;
		$data['ghg_intensity_annual_target']      = $ghg_intensity_annual_target;
		$data['water_intensity_annual_target']      = $water_intensity_annual_target;
		$data['waste_intensity_annual_target']      = $waste_intensity_annual_target;
		$data['energy_intensity_benchmark_target']      = $energy_intensity_benchmark_target;
		$data['ghg_intensity_benchmark_target']      = $ghg_intensity_benchmark_target;
		$data['water_intensity_benchmark_target']      = $water_intensity_benchmark_target;
		$data['waste_intensity_benchmark_target']      = $waste_intensity_benchmark_target;


		$data['daily_reading_utilities_list'] = $daily_reading_utilities_list;

		$data['read_daily_reading_utilites_setting'] = $read_daily_reading_utilites_setting;
		$data['residence_types'] = $residence_types;
		$data['is_used_in_cron'] = $is_used_in_cron;

		$data['threshold'] = $threshold;



		$data['status'] = $status;

		//create breadcrumbs & page-title

		if (empty($result)) {

			$this->theme->set('page_title', lang('add-site'));

			$this->breadcrumb->add(lang('add-site'));
		} else {

			$this->theme->set('page_title', lang('edit-site'));

			$this->breadcrumb->add(lang('edit-site'));
		}

		//Render view

		$this->theme->view($data, 'admin_add');
	}

	public function edit($id = 0)
	{
		$id = intval($id);
		if (is_uploaded_file($_FILES['importfile']['tmp_name'])) {
			require_once BASE_PATH_CUSTOM . '/application/libraries/Excel/excel_reader2.php';
			$file_tmp  = $_FILES['importfile']['tmp_name'];
			$file_name = $_FILES['importfile']['name'];
			$fileType  = pathinfo($file_name, PATHINFO_EXTENSION);
			if ($fileType == "") {
				$this->theme->set_message("Please upload file type with .xls extension.", 'error');
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);
				exit;
			} else if ($fileType != "xls") {
				$this->theme->set_message("File type with .xls extension is allowed.", 'error');
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);
				exit;
			} else {
				$this->load->model('utilities/utilities_model');

				$data = new Spreadsheet_Excel_Reader($file_tmp, false);

				$fieldNamesArray = array(
					'site_id',
					'measure_id',
					'low',
					'lower_quartile',
					'mean',
					'median',
					'upper_quartile',
					'high',
					'sd'
				);
				$numberRow = $data->sheets[0]['numRows'];
				$numberCol = $data->sheets[0]['numCols'];

				/* Number Of columns define */
				$colmuns['Site Name']         = "site_id";
				$colmuns['Measure']           = "measure_id";
				$colmuns['Count']             = "count";
				$colmuns['Low']               = "low";
				$colmuns['Lower Quartile']    = "lower_quartile";
				$colmuns['Mean']              = "mean";
				$colmuns['Median']            = "median";
				$colmuns['Upper Quartile']    = "upper_quartile";
				$colmuns['High']              = "high";
				$colmuns['SD']                = "sd";

				/* Number Of columns define */
				$sites_name   = array();
				$k            = 0;
				$totalCol     = 0;
				for ($i = 1; $i < 2; $i++) {
					for ($j = 1; $j <= $numberCol; $j++) {
						if ($data->sheets[0]['cells'][1][$j] != '') {
							$totalCol++;
						}
					}
				}

				// Get all site id for imported data
				$siteNames = array();
				for ($i = 2; $i <= $numberRow; $i++) {
					$siteNames[] = trim($data->sheets[0]['cells'][$i][1]);
				}
				$siteNames  = array_values(array_unique($siteNames));
				$allSiteids = $this->import_model->get_site_detail_by_name($siteNames);

				/* Start Of Number of rows*/
				for ($i = 2; $i <= $numberRow; $i++) {
					$statusInsert        = 0;
					$statusInsertUtility = 0;
					$extraData           = array();
					$dataInsertTotal     = array();
					$dataInsert          = array();

					if ($data->sheets[0]['cells'][$i][1] == '') {
						//$this->theme->set_message("Site do not Exists.", 'error');
						continue;
					}

					$siteId = $allSiteids[trim($data->sheets[0]['cells'][$i][1])]['id'];

					$measure_name = trim($data->sheets[0]['cells'][$i][2]);
					$dataInsert['measure_name'] = $measure_name;
					$dataInsert['measure_code'] = explode(':',$measure_name)[0];
					$measure_id = $this->import_model->get_measureId($measure_name);

					if ($siteId == '') {
						$sites_name[] = $data->sheets[0]['cells'][$i][1];
						continue;
					} else {
						$dataInsertTotal[$colmuns[trim($data->sheets[0]['cells'][1][1])]] = $siteId;
						$dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][1])]]      = $siteId;
					}

					for ($j = 2; $j <= $totalCol; $j++) {
						if (trim($data->sheets[0]['cells'][$i][$j]) != '') {
							$dataInsert[$colmuns[trim($data->sheets[0]['cells'][1][$j])]] = $data->sheets[0]['cells'][$i][$j];
						} else {
							continue;
						}
					}

					$dataInsert['measure_id'] = $measure_id[0]['measures']['id'];

					$n = 1;
					foreach ($dataInsert as $key => $value) {
						if (in_array($key, $fieldNamesArray)) {
							$n++;
						}
					}

					if ($n == 10) {
						$this->import_model->delete_measure_entry_ifexist($dataInsert);
						$statusInsertUtility = $this->import_model->insert_site_measures_reading($dataInsert);
						unset($dataInsert);
					} else {
						//continue;
						$this->theme->set_message("Please import excelsheet with proper data", 'error');
						redirect(BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $id);
					}
					$k++;

					if ($statusInsertUtility) {
						$this->theme->set_message("File imported successfully.", 'success');
						unlink($target_file);
					}
				}
				/* End Of Number of rows*/
			}
			/*
	    //Create page-title
	    $this->theme->set('page_title', lang('sites'));
	    if (!empty($sites_name)) {
		$site_names = implode(',', $sites_name);
		$this->theme->set_message("Sites - " . $site_names . " do not Exists.", 'error');
	    }
	    //Render view
	    redirect(BASE_ADMIN_URL_CUSTOM . 'sites');*/
		}

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$site_id = $this->session->userdata[$this->section_name]['site_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];

		//filter array to get energy modelling data
		$filterArray = [
			'site_id' => $id,
			'year'    => date('Y'),
		];

		$this->sites_model->site_id = $id;

		if ($this->input->post('mysubmit')) {
			$data = $this->input->post();
			$id   = intval($data['id']);
			$sitedetail = $this->sites_model->get_site_detail($id, $user_id, $role_id);

			if (!empty($id) && isset($id) && !empty($data['siteArea']) && isset($data['siteArea']) && $data['siteArea']['area_update_type'] == 1) {
				$dataFetch['site_id'] = $id;
				$dataFetch['area_update_field'] = $data['siteArea']['area_update_field'];
				$latestAreaEntry = $this->sites_model->getlatestSiteArea($dataFetch);
				if (isset($latestAreaEntry) && !empty($latestAreaEntry)) {
					$latestAreaEntry = (array) $latestAreaEntry;
					$oldValue = $latestAreaEntry['area_update_value'];
				} else {
					$oldValue = $sitedetail[$data['siteArea']['area_update_field']];
				}
				if ($oldValue != $data['siteArea']['area_update_value'] && !empty($data['siteArea']['area_update_value']) && isset($data['siteArea']['area_update_value'])) {
					$date = date_create($data['siteArea']['area_update_date']);
					$dataSiteArea['site_id'] = $id;
					$dataSiteArea['area_update_field'] = $data['siteArea']['area_update_field'];
					$dataSiteArea['area_update_date'] = date_format($date, "Y-m-d");
					$dataSiteArea['area_old_value'] = $oldValue;
					$dataSiteArea['area_update_value'] = $data['siteArea']['area_update_value'];
					$dataSiteArea['created_by'] = $user_id;
					$dataSiteArea['created_at'] = GetCurrentDateTime();
					$this->sites_model->updateSiteAreas($dataSiteArea, $site_location_name);

					// START - Update Residence Float area percentage with respect to new built_up_area
					if (($dataSiteArea['area_update_field'] == 'site_builtup_area') && isset($sitedetail['residence_types'])) {
						$residenceTypes = explode(',', $sitedetail['residence_types']);
						$this->load->model('sites/site_residence_model');
						foreach ($residenceTypes as $key => $value) {
							if (in_array($value, [RENTAL_PROGRAM_RESIDENCE, PRIVATE_RESIDENCE])) {
								$this->site_residence_model->site_id = $id;
								$this->site_residence_model->year_id = NULL;
								$this->site_residence_model->utility_type = NULL;
								$dataFloatPercent = [];
								$dataFloatPercent['site_builtup_area'] = $dataSiteArea['area_update_value'];
								if (($value == RENTAL_PROGRAM_RESIDENCE) && isset($sitedetail['rental_program_residence']) && !empty($sitedetail['rental_program_residence'])) {
									$this->site_residence_model->rental_program_residence_consumption = 2;
									$this->site_residence_model->private_program_consumption = NULL;
									$siteWithFloatRentalConsumptions = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
									if (isset($siteWithFloatRentalConsumptions) && !empty($siteWithFloatRentalConsumptions)) {
										$dataFloatPercent['rental_program_residence'] = $sitedetail['rental_program_residence'];
										$updatedPercentage = $this->site_residence_model->calculateFloatPercentage($dataFloatPercent, RENTAL_PROGRAM_RESIDENCE);
										$this->site_residence_model->updatePercentage($updatedPercentage, 'rental_program_residence_float', 'rental_program_residence_consumption');
									}
								} else if (($value == PRIVATE_RESIDENCE) && isset($sitedetail['rental_private_residence']) && !empty($sitedetail['rental_private_residence'])) {
									$this->site_residence_model->rental_program_residence_consumption = NULL;
									$this->site_residence_model->private_program_consumption = 2;
									$siteWithFloatPrivateConsumptions = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
									if (isset($siteWithFloatPrivateConsumptions) && !empty($siteWithFloatPrivateConsumptions)) {
										$dataFloatPercent['rental_private_residence'] = $sitedetail['rental_private_residence'];
										$updatedPercentage = $this->site_residence_model->calculateFloatPercentage($dataFloatPercent, PRIVATE_RESIDENCE);
										$this->site_residence_model->updatePercentage($updatedPercentage, 'private_program_float', 'private_program_consumption');
									}
								}
							}
						}
					}
					// END - Update Residence Float area percentage with respect to new built_up_area
				}
			} else if (empty($id) && !isset($id) && !empty($data['siteArea']) && isset($data['siteArea']) && $data['siteArea']['area_update_type'] == 1) {
				$data[$data['siteArea']['area_update_field']] = $data['siteArea']['area_update_value'];
			}

			if (empty($sitedetail['site_logo']) || !empty($_FILES["site_logo"]["tmp_name"])) {
				$this->form_validation->set_rules('site_logo', lang('hotel-logo'), 'callback_valid_upload');
			}
			$hotel_name              = trim(strip_tags($data['hotel_name']));
			$site_location_name      = trim(strip_tags($data['site_location_name']));
			$residence_types         = isset($data['residence_types']) ? implode(',', $data['residence_types']) : NULL;
			$city  = $data['city'];
			$site_location_latitude  = $data['site_location_latitude'];
			$site_location_longitude = $data['site_location_longitude'];
			$station_id              = $data['station_id'];
			$base_cdd_temprature     = $data['base_cdd_temprature'];
			$base_hdd_temprature     = $data['base_hdd_temprature'];
			$region_id               = intval($data['region_id']);
			$country_id              = intval($data['country_id']);
			$hotel_id                = intval(1); //intval($data['hotel_id']);
			$site_type               = intval($data['site_type']);
			$attribute               = $data['attribute'];
			$residences_attribute    = $data['residences_attribute'];
			$rental_program_attribute = $data['rental_program_attribute'];
			$employee_quarter_attribute                    = $data['employee_quarter_attribute'];
			$site_year_built         = intval($data['site_year_built']);

			$site_builtup_area       = isset($data['site_builtup_area']) ? $data['site_builtup_area'] : 0;

			$cooled_builtup_area     = isset($data['cooled_builtup_area']) ? $data['cooled_builtup_area'] : 0;

			$total_meeting_area      = $data['total_meeting_area'];

			$total_spa_area          = $data['total_spa_area'];
			$room_area_rental_program          = $data['room_area_rental_program'];
			$room_area_private_residence          = $data['room_area_private_residence'];
			$hotel_rooms_area          = isset($data['hotel_rooms_area']) ? $data['hotel_rooms_area'] : 0;
			$residential_common_area          = $data['residential_common_area'];
			$employee_living_quarters_area          = $data['employee_living_quarters_area'];
			$f_b_service          = $data['f_b_service'];
			$restaurant_area          = $data['restaurant_area'];
			$landscaped_area          = $data['landscaped_area'];
			$comments          = $data['comments'];
			$f_b_services_operated          = $data['f_b_services_operated'];
			$f_b_services_outsourced          = $data['f_b_services_outsourced'];
			$month_year_operation          = $data['month_year_operation'];
			$vehicle_electric          = $data['vehicle_electric'];
			$vehicle_petrol          = $data['vehicle_petrol'];
			$rental_program_residence          = $data['rental_program_residence'];
			$rental_private_residence          = $data['rental_private_residence'];
			$rental_program_residence_conditioned          = $data['rental_program_residence_conditioned'];
			$rental_private_residence_conditioned          = $data['rental_private_residence_conditioned'];
			$rental_program_residence_suites          = $data['rental_program_residence_suites'];
			$rental_private_residence_suites          = $data['rental_private_residence_suites'];
			$total_guest_room_area   = $data['total_guest_room_area'];
			$indoor_parking_area     = $data['indoor_parking_area'];
			$rooms_keys              = intval($data['rooms_keys']);
			$outdoor_pools           = intval($data['outdoor_pools']);
			$indoor_pools            = intval($data['indoor_pools']);
			$laundry_type            = intval($data['laundry_type']);

			$show_utility_electricity      = intval($data['show_utility_electricity']);
			$show_utility_fuel_oil         = intval($data['show_utility_fuel_oil']);
			$show_utility_lpg              = intval($data['show_utility_lpg']);
			$show_utility_water            = intval($data['show_utility_water']);
			$show_utility_irrigation_water = intval($data['show_utility_irrigation_water']);
			$show_utility_natural_gas      = intval($data['show_utility_natural_gas']);
			$show_utility_water_waste      = intval($data['show_utility_water_waste']);
			$show_utility_district_cooling = intval($data['show_utility_district_cooling']);
			$show_utility_district_heating = intval($data['show_utility_district_heating']);
			$show_utility_district_heating_boiler = intval($data['show_utility_district_heating_boiler']);
			$show_utility_fleet         = intval($data['show_utility_fleet']);
			$show_waste_management         = intval($data['show_waste_management']);
			$utility_unit_electricity      = intval($data['utility_unit_electricity']);
			$utility_unit_fuel_oil         = intval($data['utility_unit_fuel_oil']);
			$utility_unit_lpg              = intval($data['utility_unit_lpg']);
			$utility_unit_water            = intval($data['utility_unit_water']);
			$utility_unit_natural_gas      = intval($data['utility_unit_natural_gas']);
			$utility_unit_district_cooling = intval($data['utility_unit_district_cooling']);
			$utility_unit_district_heating = intval($data['utility_unit_district_heating']);

			$chsb_reporting                = intval($data['chsb_reporting']);
			$chsb_segment                = intval($data['chsb_segment']);
			$csr                           = intval($data['csr']);
			$daily_metering                = intval($data['daily_metering']);
			$is_hourly                     = intval($data['is_hourly']);

			$energy_intensity_annual_target                     = $data['energy_intensity_annual_target'];
			$ghg_intensity_annual_target                     = $data['ghg_intensity_annual_target'];
			$water_intensity_annual_target                     = $data['water_intensity_annual_target'];
			$waste_intensity_annual_target                     = $data['waste_intensity_annual_target'];
			$energy_intensity_benchmark_target                     = $data['energy_intensity_benchmark_target'];
			$ghg_intensity_benchmark_target                     = $data['ghg_intensity_benchmark_target'];
			$water_intensity_benchmark_target                     = $data['water_intensity_benchmark_target'];
			$waste_intensity_benchmark_target                     = $data['waste_intensity_benchmark_target'];

			$show_total_utility_notification = intval($data['show_total_utility_notification']);

			$laundry_fuel_type       = trim(strip_tags($data['laundry_fuel_type']));
			$is_chilled_water_system = intval($data['is_chilled_water_system']);
			$is_used_in_cron = intval($data['is_used_in_cron']);
			$threshold = $data['threshold'];
			if ($is_chilled_water_system == 1) {
				$chilled_water_system_type       = trim(strip_tags($data['chilled_water_system_type']));
				$chilled_water_system_total_rate = $data['chilled_water_system_total_rate'];
			} else {
				$chilled_water_system_type       = '';
				$chilled_water_system_total_rate = '';
			}

			$is_split_dx_unit = intval($data['is_split_dx_unit']);
			if ($is_split_dx_unit == 1) {
				$total_split_dx_unit      = intval($data['total_split_dx_unit']);
				$total_rate_split_dx_unit = $data['total_rate_split_dx_unit'];
			} else {
				$total_split_dx_unit      = '';
				$total_rate_split_dx_unit = '';
			}

			$is_vrv = intval($data['is_vrv']);
			if ($is_vrv == 1) {
				$total_vrv      = intval($data['total_vrv']);
				$total_vrv_unit = $data['total_vrv_unit'];
			} else {
				$total_vrv      = '';
				$total_vrv_unit = '';
			}

			$calorifiers_unit   = $data['calorifiers_unit'];
			$calorifiers_volume = $data['calorifiers_volume'];

			$chilled_water_system_type2       = $data['chilled_water_system_type2'];
			$chilled_water_system_total_rate2 = $data['chilled_water_system_total_rate2'];

			$elcetrical_hw_total          = intval($data['elcetrical_hw_total']);
			$elcetrical_hw_total_capacity = $data['elcetrical_hw_total_capacity'];
			$elcetrical_hw_total_power    = $data['elcetrical_hw_total_power'];
			$is_ro_plant                  = intval($data['is_ro_plant']);
			if ($is_ro_plant == 1) {
				$ro_plant_capacity = $data['ro_plant_capacity'];
			} else {
				$ro_plant_capacity = '';
			}

			$is_renewable_energy = intval($data['is_renewable_energy']);
			if ($is_renewable_energy == 1) {
				$data['renewable_energy'] = $data['renewable_energy'];
			} else {
				$data['renewable_energy'] = array();
			}

			$is_stp = intval($data['is_stp']);
			if ($is_stp == 1) {
				$stp_capacity = $data['stp_capacity'];
			} else {
				$stp_capacity = '';
			}

			$site_color                       = trim(strip_tags($data['site_color']));
			$electricity_emission_factor      = $data['electricity_emission_factor'];
			$fuel_emission_factor             = $data['fuel_emission_factor'];
			$lpg_emission_factor              = $data['lpg_emission_factor'];
			$natural_gas_emission_factor      = $data['natural_gas_emission_factor'];
			$district_cooling_emission_factor = $data['district_cooling_emission_factor'];
			$district_heating_emission_factor = $data['district_heating_emission_factor'];
			if ($id == 0) {
				$status = $data['status'];
			} else {
				$status = $data['status'];
			}

			$this->sites_validation_rules();

			if ($this->form_validation->run($this)) {
				if (isset($_FILES['site_logo']['name'])) {
					$config['upload_path']    = BASE_PATH_CUSTOM . "/assets/uploads/";
					$config['max_size']       = '2048';
					$config['maintain_ratio'] = true;
					$config['width']          = 140;
					$config['height']         = 100;

					$this->load->library('upload', $config);
					$this->upload->initialize($config);

					$valid_formats = array("jpg", "png");
					$imagename     = $_FILES['site_logo']['name'];

					$size = $_FILES['site_logo']['size'];
					$i    = strrpos($imagename, ".");
					if (!$i) {
						$ext = '';
					}
					$l              = strlen($imagename) - $i;
					$ext            = substr($imagename, $i + 1, $l);
					$site_logo_name = 'site_logo_' . rand(11111, 9999999) . '.' . $ext;
					if ($ext) {
						if (in_array($ext, $valid_formats)) {
							// procedure further if and only if image size can not be more than 10MB.
							if ($size < (1024 * 1024 * 10)) {
								$uploadedfile = $_FILES['site_logo']['tmp_name'];
								$target_file  = BASE_PATH_CUSTOM . "/assets/uploads/" . $site_logo_name;
								$_movestatus  = move_uploaded_file($uploadedfile, $target_file);

								if (!$_movestatus) {
									$this->theme->set_message('site image is not uploaded', 'error');
								} else {
									$this->load->library('image_lib');
									$config['image_library'] = 'gd2';
									$config['source_image']  = $target_file;
									$this->image_lib->clear();
									$this->image_lib->initialize($config);

									if (!$this->image_lib->resize()) {
										echo $this->image_lib->display_errors();
									}

									$site_logo               = trim(strip_tags($site_logo_name));
									$data_array['site_logo'] = $site_logo;

									// Delete Old file
									$oldfile = BASE_PATH_CUSTOM . "/assets/uploads/" . $sitedetail['site_logo'];
									if (file_exists($oldfile)) {
										unlink($oldfile);
									}
								}
							} else {
								$this->theme->set_message('site image size is too large', 'error');
							}
						} else {
							$this->theme->set_message('site image extension is not .jpg or .png formate', 'error');
						}
					}
				}

				$data_array['id']                              = $id;
				$data_array['hotel_name']                      = $hotel_name;
				$data_array['site_location_name']              = $site_location_name;
				$data_array['residence_types']                  = $residence_types;
				$data_array['city']          = $city;
				$data_array['site_location_latitude']          = $site_location_latitude;
				$data_array['site_location_longitude']         = $site_location_longitude;
				$data_array['station_id']                      = $station_id;
				$data_array['base_cdd_temprature']             = $base_cdd_temprature;
				$data_array['base_hdd_temprature']             = $base_hdd_temprature;
				$data_array['region_id']                       = $region_id;
				$data_array['country_id']                      = $country_id;
				$data_array['hotel_id']                        = $hotel_id;
				$data_array['site_type']                       = $site_type;
				$data_array['attribute']                       = $attribute;
				$data_array['residences_attribute']            = $residences_attribute;
				$data_array['rental_program_attribute']        = $rental_program_attribute;
				$data_array['employee_quarter_attribute']        = $employee_quarter_attribute;
				$data_array['site_year_built']                 = $site_year_built;
				$data_array['site_builtup_area']               = $site_builtup_area;
				$data_array['cooled_builtup_area']             = $cooled_builtup_area;
				$data_array['total_meeting_area']              = $total_meeting_area;
				$data_array['total_spa_area']                  = $total_spa_area;
				$data_array['room_area_rental_program']                  = $room_area_rental_program;
				$data_array['room_area_private_residence']                  = $room_area_private_residence;
				$data_array['hotel_rooms_area']                  = $hotel_rooms_area;
				$data_array['residential_common_area']                  = $residential_common_area;
				$data_array['employee_living_quarters_area']                  = $employee_living_quarters_area;
				$data_array['f_b_service']                  = $f_b_service;
				$data_array['restaurant_area']                  = $restaurant_area;
				$data_array['landscaped_area']                  = $landscaped_area;
				$data_array['comments']                  = $comments;
				$data_array['f_b_services_operated']                  = $f_b_services_operated;
				$data_array['f_b_services_outsourced']                  = $f_b_services_outsourced;
				$data_array['month_year_operation']                  = $month_year_operation;
				$data_array['vehicle_electric']                  = $vehicle_electric;
				$data_array['vehicle_petrol']                  = $vehicle_petrol;
				$data_array['rental_program_residence']                  = $rental_program_residence;
				$data_array['rental_private_residence']                  = $rental_private_residence;
				$data_array['rental_program_residence_conditioned']                  = $rental_program_residence_conditioned;
				$data_array['rental_private_residence_conditioned']                  = $rental_private_residence_conditioned;
				$data_array['rental_program_residence_suites']                  = $rental_program_residence_suites;
				$data_array['rental_private_residence_suites']                  = $rental_private_residence_suites;
				$data_array['total_guest_room_area']           = $total_guest_room_area;
				$data_array['indoor_parking_area']             = $indoor_parking_area;
				$data_array['rooms_keys']                      = $rooms_keys;
				$data_array['outdoor_pools']                   = $outdoor_pools;
				$data_array['indoor_pools']                    = $indoor_pools;
				$data_array['laundry_type']                    = $laundry_type;
				$data_array['laundry_fuel_type']               = $laundry_fuel_type;
				$data_array['is_chilled_water_system']         = $is_chilled_water_system;
				$data_array['is_used_in_cron']         = $is_used_in_cron;
				$data_array['threshold']         = $threshold;
				$data_array['chilled_water_system_type']       = $chilled_water_system_type;
				$data_array['chilled_water_system_total_rate'] = $chilled_water_system_total_rate;
				$data_array['is_split_dx_unit']                = $is_split_dx_unit;
				$data_array['total_split_dx_unit']             = $total_split_dx_unit;
				$data_array['total_rate_split_dx_unit']        = $total_rate_split_dx_unit;
				$data_array['is_vrv']                          = $is_vrv;
				$data_array['total_vrv']                       = $total_vrv;
				$data_array['total_vrv_unit']                  = $total_vrv_unit;

				$data_array['calorifiers_unit']                 = $calorifiers_unit;
				$data_array['calorifiers_volume']               = $calorifiers_volume;
				$data_array['chilled_water_system_type2']       = $chilled_water_system_type2;
				$data_array['chilled_water_system_total_rate2'] = $chilled_water_system_total_rate2;

				$data_array['elcetrical_hw_total']              = $elcetrical_hw_total;
				$data_array['elcetrical_hw_total_capacity']     = $elcetrical_hw_total_capacity;
				$data_array['elcetrical_hw_total_power']        = $elcetrical_hw_total_power;
				$data_array['is_ro_plant']                      = $is_ro_plant;
				$data_array['ro_plant_capacity']                = $ro_plant_capacity;
				$data_array['is_renewable_energy']              = $is_renewable_energy;
				$data_array['is_stp']                           = $is_stp;
				$data_array['stp_capacity']                     = $stp_capacity;
				$data_array['site_color']                       = $site_color;
				$data_array['electricity_emission_factor']      = $electricity_emission_factor;
				$data_array['fuel_emission_factor']             = $fuel_emission_factor;
				$data_array['lpg_emission_factor']              = $lpg_emission_factor;
				$data_array['natural_gas_emission_factor']      = $natural_gas_emission_factor;
				$data_array['district_cooling_emission_factor'] = $district_cooling_emission_factor;
				$data_array['district_heating_emission_factor'] = $district_heating_emission_factor;

				$data_array['show_utility_electricity']        = $show_utility_electricity;
				$data_array['show_utility_fuel_oil']           = $show_utility_fuel_oil;
				$data_array['show_utility_lpg']                = $show_utility_lpg;
				$data_array['show_utility_water']              = $show_utility_water;
				$data_array['show_utility_irrigation_water']   = $show_utility_irrigation_water;
				$data_array['show_utility_natural_gas']        = $show_utility_natural_gas;
				$data_array['show_utility_water_waste']        = $show_utility_water_waste;
				$data_array['show_utility_district_cooling']   = $show_utility_district_cooling;
				$data_array['show_utility_district_heating']   = $show_utility_district_heating;
				$data_array['show_utility_district_heating_boiler']   = $show_utility_district_heating_boiler;

				$data_array['utility_unit_electricity']        = $utility_unit_electricity;
				$data_array['utility_unit_fuel_oil']           = $utility_unit_fuel_oil;
				$data_array['utility_unit_lpg']                = $utility_unit_lpg;
				$data_array['utility_unit_water']              = $utility_unit_water;
				$data_array['utility_unit_natural_gas']        = $utility_unit_natural_gas;
				$data_array['utility_unit_district_cooling']   = $utility_unit_district_cooling;
				$data_array['utility_unit_district_heating']   = $utility_unit_district_heating;

				$data_array['show_waste_management']           = $show_waste_management;
				$data_array['show_utility_fleet']           = $show_utility_fleet;

				$data_array['baseline_regression_year']        = trim($data['baseline_regression_year']);
				$data_array['local_currency']                  = trim($data['local_currency']);
				$data_array['local_unit']                  = trim($data['local_unit']);
				$data_array['show_total_utility_notification'] = $show_total_utility_notification;
				$data_array['chsb_reporting']                  = $chsb_reporting;
				$data_array['chsb_segment']                  = $chsb_segment;
				$data_array['csr']                             = $csr;
				$data_array['daily_metering']                  = $daily_metering;
				$data_array['is_hourly']                       = $is_hourly;

				$data_array['energy_intensity_annual_target']                       = $energy_intensity_annual_target;
				$data_array['ghg_intensity_annual_target']                       = $ghg_intensity_annual_target;
				$data_array['water_intensity_annual_target']                       = $water_intensity_annual_target;
				$data_array['waste_intensity_annual_target']                       = $waste_intensity_annual_target;
				$data_array['energy_intensity_benchmark_target']                       = $energy_intensity_benchmark_target;
				$data_array['ghg_intensity_benchmark_target']                       = $ghg_intensity_benchmark_target;
				$data_array['water_intensity_benchmark_target']                       = $water_intensity_benchmark_target;
				$data_array['waste_intensity_benchmark_target']                       = $waste_intensity_benchmark_target;

				$data_array['status']  = $status;
				$data_array['user_id'] = $this->session->userdata[$this->section_name]['user_id'];
                
				$site_id = $this->sites_model->save_site($data_array);
				//set changed local currency to session
				$this->session->set_custom_userdata($this->section_name, "local_currency", $data_array['local_currency']);
				//set changed local unit to session
				$this->session->set_custom_userdata($this->section_name, "local_unit", $data_array['local_unit']);
				//data to pass for saving energy modeling data
				$energy_data = $data['energy_modeling'];
				foreach ($energy_data as $key => $energy) {
					$energy['year_id'] = date('Y');
					$energy['site_id'] = $site_id;
					$energy_data[$key] = $energy;
				}
				$this->sites_model->save_energy_modelling($energy_data);

				$this->sites_model->save_substation($site_id, $data['substation']);
				$this->sites_model->save_generator($site_id, $data['generator']);
				$this->sites_model->save_hot_water_boiler($site_id, $data['hot_water_boiler']);
				$this->sites_model->save_steam_boiler($site_id, $data['steam_boiler']);
				$this->sites_model->save_renewable_energy($site_id, $data['renewable_energy'], $is_renewable_energy);
				$this->sites_model->save_daily_reading_settings($site_id, $data['daily_reading'], $data['daily_reading']);
				$this->sites_model->save_daily_reading_utilites_setting($site_id, $data['daily_reading_utlities']);

				if (empty($data_array['id'])) {
					$site_notification_lists = getNotificationStaticList($site_id);
					$this->sites_model->save_default_notifications($site_id, $site_notification_lists);
				}
				$this->sites_model->assign_site_to_corporate_and_super_admins($site_id, isset($data_array['region_id']) ? $data_array['region_id'] : 0);

				if ($id == 0) {
					$this->theme->set_message(lang('site-add-success'), 'success');
				} else {
					$this->theme->set_message(lang('site-edit-success'), 'success');
				}

				// redirect(BASE_ADMIN_URL_CUSTOM . 'sites');
			}
		}

		$result = $this->sites_model->get_site_detail($id, $user_id, $role_id);
		$data['site_id'] = 0;
		if (!empty($result)) {

			$data['site_id'] = $result['id'];
			//Variable assignment for edit view
			$hotel_name                       = $result['hotel_name'];
			$site_location_name               = $result['site_location_name'];
			$residence_types                  = isset($result['residence_types']) ? explode(',', $result['residence_types']) : [];
			$city           = $result['city'];
			$site_location_latitude           = $result['site_location_latitude'];
			$site_location_longitude          = $result['site_location_longitude'];
			$station_id                       = $result['station_id'];
			$base_cdd_temprature              = $result['base_cdd_temprature'];
			$base_hdd_temprature              = $result['base_hdd_temprature'];
			$region_id                        = $result['region_id'];
			$country_id                       = $result['country_id'];
			$hotel_id                         = $result['hotel_id'];
			$site_type                        = $result['site_type'];
			$attribute                        = $result['attribute'];
			$residences_attribute             = $result['residences_attribute'];
			$rental_program_attribute         = $result['rental_program_attribute'];
			$employee_quarter_attribute         = $result['employee_quarter_attribute'];
			$site_year_built                  = $result['site_year_built'];
			$site_builtup_area                = $result['site_builtup_area'];
			$cooled_builtup_area              = $result['cooled_builtup_area'];
			$total_meeting_area               = $result['total_meeting_area'];
			$total_spa_area                   = $result['total_spa_area'];
			$room_area_rental_program                   = $result['room_area_rental_program'];
			$room_area_private_residence                   = $result['room_area_private_residence'];
			$hotel_rooms_area                   = $result['hotel_rooms_area'];
			$residential_common_area                   = $result['residential_common_area'];
			$employee_living_quarters_area                   = $result['employee_living_quarters_area'];
			$f_b_service                   = $result['f_b_service'];
			$restaurant_area                   = $result['restaurant_area'];
			$landscaped_area                   = $result['landscaped_area'];
			$comments                   = $result['comments'];
			$f_b_services_operated                   = $result['f_b_services_operated'];
			$f_b_services_outsourced                   = $result['f_b_services_outsourced'];
			$month_year_operation                   = $result['month_year_operation'];
			$vehicle_electric                   = $result['vehicle_electric'];
			$vehicle_petrol                   = $result['vehicle_petrol'];
			$rental_program_residence                   = $result['rental_program_residence'];
			$rental_private_residence                   = $result['rental_private_residence'];
			$rental_program_residence_conditioned                   = $result['rental_program_residence_conditioned'];
			$rental_private_residence_conditioned                   = $result['rental_private_residence_conditioned'];
			$rental_program_residence_suites                   = $result['rental_program_residence_suites'];
			$rental_private_residence_suites                   = $result['rental_private_residence_suites'];
			$total_guest_room_area            = $result['total_guest_room_area'];
			$indoor_parking_area              = $result['indoor_parking_area'];
			$rooms_keys                       = $result['rooms_keys'];
			$outdoor_pools                    = $result['outdoor_pools'];
			$indoor_pools                     = $result['indoor_pools'];
			$laundry_type                     = isset($result['laundry_type']) ? $result['laundry_type'] : 1;
			$laundry_fuel_type                = $result['laundry_fuel_type'];
			$is_chilled_water_system          = isset($result['is_chilled_water_system']) ? $result['is_chilled_water_system'] : 1;
			$chilled_water_system_type        = $result['chilled_water_system_type'];
			$chilled_water_system_total_rate  = $result['chilled_water_system_total_rate'];
			$is_split_dx_unit                 = isset($result['is_split_dx_unit']) ? $result['is_split_dx_unit'] : 1;
			$total_split_dx_unit              = $result['total_split_dx_unit'];
			$total_rate_split_dx_unit         = $result['total_rate_split_dx_unit'];
			$is_vrv                           = isset($result['is_vrv']) ? $result['is_vrv'] : 1;
			$total_vrv                        = $result['total_vrv'];
			$total_vrv_unit                   = $result['total_vrv_unit'];
			$calorifiers_unit                 = $result['calorifiers_unit'];
			$calorifiers_volume               = $result['calorifiers_volume'];
			$chilled_water_system_type2       = $result['chilled_water_system_type2'];
			$chilled_water_system_total_rate2 = $result['chilled_water_system_total_rate2'];
			$elcetrical_hw_total              = $result['elcetrical_hw_total'];
			$elcetrical_hw_total_capacity     = $result['elcetrical_hw_total_capacity'];
			$elcetrical_hw_total_power        = $result['elcetrical_hw_total_power'];
			$is_ro_plant                      = isset($result['is_ro_plant']) ? $result['is_ro_plant'] : 1;
			$ro_plant_capacity                = $result['ro_plant_capacity'];
			$is_renewable_energy              = isset($result['is_renewable_energy']) ? $result['is_renewable_energy'] : 1;
			$is_stp                           = isset($result['is_stp']) ? $result['is_stp'] : 1;
			$stp_capacity                     = $result['stp_capacity'];
			$site_logo                        = $result['site_logo'];
			$site_color                       = $result['site_color'];
			$electricity_emission_factor      = $result['electricity_emission_factor'];
			$fuel_emission_factor             = $result['fuel_emission_factor'];
			$lpg_emission_factor              = $result['lpg_emission_factor'];
			$natural_gas_emission_factor      = $result['natural_gas_emission_factor'];
			$district_cooling_emission_factor = $result['district_cooling_emission_factor'];
			$district_heating_emission_factor = $result['district_heating_emission_factor'];
			$status                           = isset($result['status']) ? $result['status'] : 1;
			$substations                      = $this->sites_model->get_substations($data['site_id']);
			$data['substations']              = $substations;
			$generators                       = $this->sites_model->get_generators($data['site_id']);
			$data['generators']               = $generators;
			$hot_water_boilers                = $this->sites_model->get_hot_water_boilers($data['site_id']);
			$data['hot_water_boilers']        = $hot_water_boilers;
			$steam_boilers                    = $this->sites_model->get_steam_boilers($data['site_id']);
			$data['steam_boilers']            = $steam_boilers;
			$renewable_energys                = $this->sites_model->get_renewable_energys($data['site_id']);
			$data['renewable_energys']        = $renewable_energys;

			$daily_reading_settings         = $this->sites_model->get_daily_reading_settings($data['site_id']);
			$data['daily_reading_settings'] = $daily_reading_settings;

			$show_utility_electricity      = $result['show_utility_electricity'];
			$show_utility_fuel_oil         = $result['show_utility_fuel_oil'];
			$show_utility_lpg              = $result['show_utility_lpg'];
			$show_utility_water            = $result['show_utility_water'];
			$show_utility_irrigation_water = $result['show_utility_irrigation_water'];
			$show_utility_natural_gas      = $result['show_utility_natural_gas'];
			$show_utility_water_waste      = $result['show_utility_water_waste'];
			$show_utility_district_cooling = $result['show_utility_district_cooling'];
			$show_utility_district_heating = $result['show_utility_district_heating'];
			$show_utility_district_heating_boiler = $result['show_utility_district_heating_boiler'];
			$show_waste_management         = $result['show_waste_management'];
			$show_utility_fleet         = $result['show_utility_fleet'];
			$chsb_reporting          = $result['chsb_reporting'];
			$chsb_segment          = $result['chsb_segment'];
			$csr                     = $result['csr'];
			$is_used_in_cron         = $result['is_used_in_cron'];
			$threshold               = $result['threshold'];
			$daily_metering          = $result['daily_metering'];
			$is_hourly               = $result['is_hourly'];
			$energy_intensity_annual_target               = $result['energy_intensity_annual_target'];
			$ghg_intensity_annual_target               = $result['ghg_intensity_annual_target'];
			$water_intensity_annual_target               = $result['water_intensity_annual_target'];
			$waste_intensity_annual_target               = $result['waste_intensity_annual_target'];
			$energy_intensity_benchmark_target               = $result['energy_intensity_benchmark_target'];
			$ghg_intensity_benchmark_target               = $result['ghg_intensity_benchmark_target'];
			$water_intensity_benchmark_target               = $result['water_intensity_benchmark_target'];
			$waste_intensity_benchmark_target               = $result['waste_intensity_benchmark_target'];

			$show_total_utility_notification = $result['show_total_utility_notification'];

			//get energy data and pass it to view data
			$energy_modelling_data            = $this->sites_model->get_energy_modelling($filterArray);
			$data['energy_modelling_data']    = $energy_modelling_data;
			$data['baseline_regression_year'] = $result['baseline_regression_year'];
			$data['local_currency']           = $result['local_currency'];
			$data['local_unit']           = $result['local_unit'];
		}

		$region_list                  = $this->sites_model->region_list();
		$hotel_list                   = $this->sites_model->hotel_list();
		$country_list                 = $this->sites_model->country_list();
		$daily_reading_utilities_list = $this->sites_model->daily_reading_utilities_list();
		$read_daily_reading_utilites_setting = $this->sites_model->read_daily_reading_utilites_setting($result['id']);
		$measure_readings         = $this->sites_model->get_measure_readings($id);
		$data['measure_readings'] = $measure_readings;
		$data['residence_types'] = $residence_types;

		// Pass data to view file
		$data['id']                               = $id;
		$data['hotel_name']                       = $hotel_name;
		$data['site_location_name']               = $site_location_name;
		$data['city']           = $city;
		$data['site_location_latitude']           = $site_location_latitude;
		$data['site_location_longitude']          = $site_location_longitude;
		$data['station_id']                       = $station_id;
		$data['base_cdd_temprature']              = $base_cdd_temprature;
		$data['base_hdd_temprature']              = $base_hdd_temprature;
		$data['region_id']                        = $region_id;
		$data['country_id']                       = $country_id;
		$data['hotel_list']                       = $hotel_list;
		$data['hotel_id']                         = $hotel_id;
		$data['site_type']                        = $site_type;
		$data['attribute']                        = $attribute;
		$data['residences_attribute']             = $residences_attribute;
		$data['rental_program_attribute']         = $rental_program_attribute;
		$data['employee_quarter_attribute']       = $employee_quarter_attribute;
		$data['region_list']                      = $region_list;
		$data['country_list']                     = $country_list;
		$data['site_year_built']                  = $site_year_built;
		$data['site_builtup_area']                = $site_builtup_area;
		$data['cooled_builtup_area']              = $cooled_builtup_area;
		$data['total_meeting_area']               = $total_meeting_area;
		$data['total_spa_area']                   = $total_spa_area;
		$data['room_area_rental_program']         = $room_area_rental_program;
		$data['room_area_private_residence']      = $room_area_private_residence;
		$data['hotel_rooms_area']                 = $hotel_rooms_area;
		$data['residential_common_area']          = $residential_common_area;
		$data['employee_living_quarters_area']    = $employee_living_quarters_area;
		$data['f_b_service']                      = $f_b_service;
		$data['restaurant_area']                  = $restaurant_area;
		$data['landscaped_area']                  = $landscaped_area;
		$data['comments']            = $comments;
		$data['f_b_services_operated']            = $f_b_services_operated;
		$data['f_b_services_outsourced']          = $f_b_services_outsourced;
		$data['month_year_operation']             = $month_year_operation;
		$data['vehicle_electric']                 = $vehicle_electric;
		$data['vehicle_petrol']                   = $vehicle_petrol;
		$data['rental_program_residence']                   = $rental_program_residence;
		$data['rental_private_residence']                   = $rental_private_residence;
		$data['rental_program_residence_conditioned']                   = $rental_program_residence_conditioned;
		$data['rental_private_residence_conditioned']                   = $rental_private_residence_conditioned;
		$data['rental_program_residence_suites']                   = $rental_program_residence_suites;
		$data['rental_private_residence_suites']                   = $rental_private_residence_suites;
		$data['total_guest_room_area']            = $total_guest_room_area;
		$data['indoor_parking_area']              = $indoor_parking_area;
		$data['rooms_keys']                       = $rooms_keys;
		$data['outdoor_pools']                    = $outdoor_pools;
		$data['indoor_pools']                     = $indoor_pools;
		$data['laundry_type']                     = $laundry_type;
		$data['laundry_fuel_type']                = $laundry_fuel_type;
		$data['is_chilled_water_system']          = $is_chilled_water_system;
		$data['chilled_water_system_type']        = $chilled_water_system_type;
		$data['chilled_water_system_total_rate']  = $chilled_water_system_total_rate;
		$data['is_split_dx_unit']                 = $is_split_dx_unit;
		$data['total_split_dx_unit']              = $total_split_dx_unit;
		$data['total_rate_split_dx_unit']         = $total_rate_split_dx_unit;
		$data['is_vrv']                           = $is_vrv;
		$data['total_vrv']                        = $total_vrv;
		$data['total_vrv_unit']                   = $total_vrv_unit;
		$data['chilled_water_system_type2']       = $chilled_water_system_type2;
		$data['chilled_water_system_total_rate2'] = $chilled_water_system_total_rate2;
		$data['calorifiers_unit']                 = $calorifiers_unit;
		$data['calorifiers_volume']               = $calorifiers_volume;
		$data['elcetrical_hw_total']              = $elcetrical_hw_total;
		$data['elcetrical_hw_total_capacity']     = $elcetrical_hw_total_capacity;
		$data['elcetrical_hw_total_power']        = $elcetrical_hw_total_power;
		$data['is_ro_plant']                      = $is_ro_plant;
		$data['ro_plant_capacity']                = $ro_plant_capacity;
		$data['is_renewable_energy']              = $is_renewable_energy;
		$data['is_stp']                           = $is_stp;
		$data['stp_capacity']                     = $stp_capacity;
		$data['site_logo']                        = $site_logo;
		$data['site_color']                       = $site_color;
		$data['electricity_emission_factor']      = $electricity_emission_factor;
		$data['fuel_emission_factor']             = $fuel_emission_factor;
		$data['lpg_emission_factor']              = $lpg_emission_factor;
		$data['natural_gas_emission_factor']      = $natural_gas_emission_factor;
		$data['district_cooling_emission_factor'] = $district_cooling_emission_factor;
		$data['district_heating_emission_factor'] = $district_heating_emission_factor;

		$data['show_utility_electricity']      = $show_utility_electricity;
		$data['show_utility_fuel_oil']         = $show_utility_fuel_oil;
		$data['show_utility_lpg']              = $show_utility_lpg;
		$data['show_utility_water']            = $show_utility_water;
		$data['show_utility_irrigation_water'] = $show_utility_irrigation_water;
		$data['show_utility_natural_gas']      = $show_utility_natural_gas;
		$data['show_utility_water_waste']      = $show_utility_water_waste;
		$data['show_utility_district_cooling'] = $show_utility_district_cooling;
		$data['show_utility_district_heating'] = $show_utility_district_heating;
		$data['show_utility_district_heating_boiler'] = $show_utility_district_heating_boiler;
		$data['show_waste_management']         = $show_waste_management;
		$data['show_utility_fleet']           = $show_utility_fleet;

		$data['show_total_utility_notification'] = $show_total_utility_notification;
		$data['chsb_reporting'] = $chsb_reporting;
		$data['chsb_segment'] = $chsb_segment;
		$data['csr']            = $csr;
		$data['daily_metering'] = $daily_metering;
		$data['is_hourly']      = $is_hourly;

		$data['energy_intensity_annual_target']      = $energy_intensity_annual_target;
		$data['ghg_intensity_annual_target']      = $ghg_intensity_annual_target;
		$data['water_intensity_annual_target']      = $water_intensity_annual_target;
		$data['waste_intensity_annual_target']      = $waste_intensity_annual_target;
		$data['energy_intensity_benchmark_target']      = $energy_intensity_benchmark_target;
		$data['ghg_intensity_benchmark_target']      = $ghg_intensity_benchmark_target;
		$data['water_intensity_benchmark_target']      = $water_intensity_benchmark_target;
		$data['waste_intensity_benchmark_target']      = $waste_intensity_benchmark_target;


		$data['daily_reading_utilities_list'] = $daily_reading_utilities_list;
		$data['read_daily_reading_utilites_setting'] = $read_daily_reading_utilites_setting;
		$data['residence_types'] = $residence_types;
		$data['is_used_in_cron'] = $is_used_in_cron;
		$data['threshold'] = $threshold;

		$data['status'] = $status;

		//create breadcrumbs & page-title
		if (empty($result)) {
			$this->theme->set('page_title', lang('add-site'));
			$this->breadcrumb->add(lang('add-site'));
		} else {
			$this->theme->set('page_title', lang('edit-site'));
			$this->breadcrumb->add(lang('edit-site'));
		}
		//Render view
		$this->theme->view($data, 'admin_edit');
	}
	public function delete()

	{

		$data = $this->input->post();

		$id   = intval(base64_decode($data['id']));



		$user_id = $this->session->userdata[$this->section_name]['user_id'];

		$site_id = $this->session->userdata[$this->section_name]['site_id'];

		$role_id = $this->session->userdata[$this->section_name]['role_id'];

		$result  = $this->sites_model->get_site_detail($id, $user_id, $role_id);



		if (!empty($result)) {

			$res = $this->sites_model->delete_site($id);

			if ($res) {

				echo $this->theme->message(lang('site-delete-success'), 'success');
			}
		} else {

			echo $this->theme->message(lang('invalid-id-msg'), 'error');
		}
	}



	public function view_data($id = 0)

	{

		$user_id              = $this->session->userdata[$this->section_name]['user_id'];

		$site_id              = $this->session->userdata[$this->section_name]['site_id'];

		$role_id              = $this->session->userdata[$this->section_name]['role_id'];

		$result               = $this->sites_model->get_site_detail($id, $user_id, $role_id);

		$region_list          = $this->sites_model->region_list();

		$country_list         = $this->sites_model->country_list();

		$hotel_list           = $this->sites_model->hotel_list();

		$data                 = array();

		$data                 = $result;

		$data['region_list']  = $region_list;

		$data['country_list'] = $country_list;

		$data['hotel_list']   = $hotel_list;



		$substations         = $this->sites_model->get_substations($data['id']);

		$data['substations'] = $substations;



		$generators         = $this->sites_model->get_generators($data['id']);

		$data['generators'] = $generators;



		$hot_water_boilers         = $this->sites_model->get_hot_water_boilers($data['id']);

		$data['hot_water_boilers'] = $hot_water_boilers;



		$steam_boilers         = $this->sites_model->get_steam_boilers($data['id']);

		$data['steam_boilers'] = $steam_boilers;



		$renewable_energys         = $this->sites_model->get_renewable_energys($data['id']);

		$data['renewable_energys'] = $renewable_energys;



		$measure_readings         = $this->sites_model->get_measure_readings($data['id']);

		$data['measure_readings'] = $measure_readings;



		$this->breadcrumb->add(lang('view-site'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites');



		$this->theme->view($data);
	}



	public function set_user_theme()

	{

		//$site_id = isset($this->session->userdata[$this->section_name]['site_id'])?$this->session->userdata[$this->section_name]['site_id']:'';

		$user_id = $this->session->userdata[$this->section_name]['user_id'];

		$role_id = $this->session->userdata[$this->section_name]['role_id'];



		$site_id = $this->input->post('site_id');



		$site_color_logo = $this->sites_model->get_site_color_logo($site_id);

		$site_info       = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);



		if (!empty($site_color_logo)) {

			$newdata['site_id']        = $site_id;

			$newdata['site_logo']      = $site_color_logo['site_logo'];

			$newdata['site_color']     = $site_color_logo['site_color'];

			$newdata['local_currency'] = $site_info['local_currency'];
			$newdata['local_unit'] = $site_info['local_unit'];
			$this->session->set_custom_userdata($this->section_name, $newdata);
		} else if ($role_id == 1) {

			$newdata['site_id']        = 0;

			$newdata['site_logo']      = '';

			$newdata['site_color']     = '';

			$newdata['local_currency'] = $site_info['local_currency'];
			$newdata['local_unit'] = $site_info['local_unit'];
			$this->session->set_custom_userdata($this->section_name, $newdata);
		}

		$newdata_cookie = json_encode($newdata);

		setcookie('hotel_theme_setting', $newdata_cookie, time() + (3600), "/");
	}

	public function setSiteNotificationManually()
	{
		$sites = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150];
		$user_id = 1;
		foreach ($sites as $key => $value) {
			$site = $this->sites_model->get_site_detail_custom($value);
			$post_notifications = [];
			if (empty($site)) {
				continue;
			} else {
				if ($site['show_utility_electricity'] == 1) {
					array_push($post_notifications, 'total_electricity_kwh');
				}

				if ($site['show_utility_lpg'] == 1) {
					array_push($post_notifications, 'total_lpg');
				}

				if ($site['show_utility_water'] == 1) {
					array_push($post_notifications, 'water_total_consumption');
				}

				if ($site['show_utility_district_cooling'] == 1) {
					array_push($post_notifications, 'district_cooling');
				}
				if (isset($post_notifications) && !empty($post_notifications)) {
					$this->sites_model->setSiteNotifications($value, $post_notifications, $user_id);
				}
			}
		}
	}


	public function set_notification($id = 0)

	{

		$id   = intval($id);

		$site = $this->sites_model->get_site_detail_custom($id);
		if (empty($site)) {

			$this->theme->set_message(lang('site-not-found-error'), 'error');

			redirect(BASE_ADMIN_URL_CUSTOM . 'sites');
		}



		// Save site notification data

		if ($this->input->post('mysubmit')) {



			$user_id = $this->session->userdata[$this->section_name]['user_id'];

			$post_notifications = $this->input->post('notifications');

			$this->sites_model->setSiteNotifications($id, $post_notifications, $user_id);



			// Custom notification

			$post_custom_notifications = $this->input->post('customnotifications');

			$post_custom_notifications = array_filter($post_custom_notifications);



			//ytd configuration

			$post_ytd_config = $this->input->post('ytd');

			$post_ytd_config = array_filter($post_ytd_config);



			//annual configuration

			$post_annual_config = $this->input->post('annual');

			$post_annual_config = array_filter($post_annual_config);



			//date configuration

			$post_date_config = $this->input->post('notification_date');

			$post_date_config = array_filter($post_date_config);



			foreach ($post_custom_notifications as $key => $value) {

				$post_custom_notifications[$key] = [

					'notification' => $value,

					'ytd'          => array_key_exists($key, $post_ytd_config) ? 1 : 0,

					'annual'       => array_key_exists($key, $post_annual_config) ? 1 : 0,

					'date'         => array_key_exists($key, $post_date_config) ? date('Y-m-d', strtotime(str_replace('/', '-', '01/' . $post_date_config[$key]))) : '',

				];
			}



			$this->sites_model->setSiteCustomNotifications($id, $post_custom_notifications);



			$this->theme->set_message(lang('site-notifications-saved'), 'success');

			redirect(BASE_ADMIN_URL_CUSTOM . 'sites');
		}



		$site_notifications = array();

		$results            = $this->sites_model->getSiteNotifications($id);

		foreach ($results as $key => $value) {

			$site_notifications[] = $value['notification_title'];
		}



		$resultArray = $this->sites_model->getSiteCustomNotifications($id);

		$site_custom_notifications = array();

		foreach ($resultArray as $key => $value) {

			if (!empty($value['date'])) {

				$site_custom_notifications[date('Y', strtotime($value['date']))][date('n', strtotime($value['date']))][] = [

					'key' => $key,

					'notification' => $value['notification'],

					'ytd' => $value['ytd'],

					'annual' => $value['annual'],

					'date' => $value['date']

				];
			}
		}



		//sort according to year and month

		ksort($site_custom_notifications);

		foreach ($site_custom_notifications as $key => $value) {

			ksort($site_custom_notifications[$key]);
		}
		$site_notification_lists = [];
		$site_notification_static_lists = getNotificationStaticList($id);
		foreach ($site_notification_static_lists as $key => $value) {
			if ($site['show_utility_electricity'] == 1) {
				$site_notification_lists['total_electricity_kwh'] = 'Total Electricity (' . GetSiteUtilityUnitName($id, 'electricity') . ')';
			}

			if ($site['show_utility_fuel_oil'] == 1) {
				$site_notification_lists['total_fuel_oil'] = 'Total Fuel Oil (' . GetSiteUtilityUnitName($id, 'fuel_oil') . ')';
			}

			if ($site['show_utility_lpg'] == 1) {
				$site_notification_lists['total_lpg'] = 'Total LPG (' . GetSiteUtilityUnitName($id, 'lpg') . ')';
			}

			if ($site['show_utility_water'] == 1) {
				$site_notification_lists['water_total_consumption'] = 'Total Water (' . GetSiteUtilityUnitName($id, 'water') . ')';
			}

			if ($site['show_utility_natural_gas'] == 1) {
				$site_notification_lists['total_natural_gas'] = 'Total Natural Gas (' . GetSiteUtilityUnitName($id, 'natural_gas') . ')';
			}

			if ($site['show_utility_district_cooling'] == 1) {
				$site_notification_lists['district_cooling'] = 'Total District Cooling (' . GetSiteUtilityUnitName($id, 'district_cooling') . ')';
			}

			if ($site['show_utility_district_heating'] == 1) {
				$site_notification_lists['district_heating'] = 'Total District Heating (' . GetSiteUtilityUnitName($id, 'district_heating') . ')';
			}
		}
		$data                              = array();

		$data['id']                        = $id;

		$data['site_notifications']        = $site_notifications;

		$data['site_custom_notifications'] = $site_custom_notifications;
		$data['notification_list']         = $site_notification_lists;
		$this->theme->set('page_title', lang('set-notification'));

		$this->breadcrumb->add(lang('set-notification'));

		$this->theme->view($data);
	}



	public function pdf($id = '')

	{

		require_once BASE_PATH_CUSTOM . '/application/libraries/tcpdf/tcpdf.php';



		$user_id              = $this->session->userdata[$this->section_name]['user_id'];

		$site_id              = $this->session->userdata[$this->section_name]['site_id'];

		$role_id              = $this->session->userdata[$this->section_name]['role_id'];

		$result               = $this->sites_model->get_site_detail($id, $user_id, $role_id);

		$region_list          = $this->sites_model->region_list();

		$country_list         = $this->sites_model->country_list();

		$hotel_list           = $this->sites_model->hotel_list();

		$data                 = array();

		$data                 = $result;

		$data['region_list']  = $region_list;

		$data['country_list'] = $country_list;

		$data['hotel_list']   = $hotel_list;



		$substations         = $this->sites_model->get_substations($data['id']);

		$data['substations'] = $substations;



		$generators         = $this->sites_model->get_generators($data['id']);

		$data['generators'] = $generators;



		$hot_water_boilers         = $this->sites_model->get_hot_water_boilers($data['id']);

		$data['hot_water_boilers'] = $hot_water_boilers;



		$steam_boilers         = $this->sites_model->get_steam_boilers($data['id']);

		$data['steam_boilers'] = $steam_boilers;



		$renewable_energys         = $this->sites_model->get_renewable_energys($data['id']);

		$data['renewable_energys'] = $renewable_energys;



		if (!empty($this->input->post())) {

			$postdata                = [];

			$postdata                = $this->input->post();

			$data['columnChartImg']  = $postdata['columnChartImg'];

			$data['pieChartImg']     = $postdata['pieChartImg'];

			$data['pieChartNewImg']  = $postdata['pieChartNewImg'];

			$data['pieChartNew2Img'] = $postdata['pieChartNew2Img'];

			$data['pieChartNew3Img'] = $postdata['pieChartNew3Img'];
		}



		$content = $this->load->view('admin_pdf', $data, true);

		$pdf     = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);



		$pdf->SetFont('helvetica', '', 9);

		$pdf->SetCreator(PDF_CREATOR);

		$pdf->SetPrintHeader(false);

		$pdf->SetPrintFooter(true);

		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		$pdf->SetMargins(3, 3, 3); // Left,Top,Right

		$pdfName = "site_details.pdf";

		$pdf->AddPage();

		ob_start();

		ob_end_clean();

		$pdf->writeHTML(sanitize_report_output_html($content), true, false, true, false, '');

		$pdf->Output($pdfName, 'D'); // D - downlaod, F- Save

		exit;
	}



	/**

	 * Function index to view listing of sites

	 */

	public function cron_settings()

	{

		$site_id = $this->session->userdata[$this->section_name]['site_id'];

		$role_id = $this->session->userdata[$this->section_name]['role_id'];

		$user_id = $this->session->userdata[$this->section_name]['user_id'];



		$this->sites_model->site_id = $this->site_id;

		$this->sites_model->role_id = $this->role_id;

		$this->sites_model->user_id = $this->user_id;



		$region_list = $this->sites_model->region_list();

		$all_region_list = $this->sites_model->all_region_list();

		$data['region_list'] = $region_list;



		$region_id = $all_region_list[0]['id'];

		$data['region_id'] = $region_id;

		$data['role_id'] = $role_id;

		$data['region_list'] = $region_list;



		if ($this->input->post()) {

			$data = $this->input->post();

			$data['region_list'] = $region_list;

			$region_id = $this->input->post('region_id');

			$data['region_id'] = $region_id;



			if ($_POST['site_submit']) {
				// Delete all records before insert new records

				$this->sites_model->deleteSiteCronSettings($region_id);
			}


			// Insert data for annual CRON settings

			$cronStatus = 1;

			foreach ($data['annual'] as $value) {

				$setData = array();

				$setData['cron_type'] = 'ANNUAL';

				$setData['site_id'] = $value;

				$setData['status'] = $cronStatus;



				$this->sites_model->saveSiteCronSettings($setData);
			}



			// Insert data for monthly CRON settings

			foreach ($data['monthly'] as $value) {

				$setData = array();

				$setData['cron_type'] = 'MONTHLY';

				$setData['site_id'] = $value;

				$setData['status'] = $cronStatus;



				$this->sites_model->saveSiteCronSettings($setData);
			}



			// Insert data for daily trends CRON settings

			foreach ($data['daily_trends'] as $value) {

				$setData = array();

				$setData['cron_type'] = 'DAILY_TRENDS';

				$setData['site_id'] = $value;

				$setData['status'] = $cronStatus;



				$this->sites_model->saveSiteCronSettings($setData);
			}
		}



		$siteCronSettings = $this->sites_model->getSiteCronSettings();

		$monthly = array();

		$annual = array();

		$daily_trends = array();

		foreach ($siteCronSettings as $cronSettings) {

			if ($cronSettings['site_cron_settings']['cron_type'] == 'MONTHLY') {

				array_push($monthly,  $cronSettings['site_cron_settings']['site_id']);
			}



			if ($cronSettings['site_cron_settings']['cron_type'] == 'ANNUAL') {

				array_push($annual,  $cronSettings['site_cron_settings']['site_id']);
			}



			if ($cronSettings['site_cron_settings']['cron_type'] == 'DAILY_TRENDS') {

				array_push($daily_trends,  $cronSettings['site_cron_settings']['site_id']);
			}
		}



		if (!empty($this->session->userdata[$this->section_name]['site_search_term'])) {

			$this->sites_model->search_term = trim($this->session->userdata[$this->section_name]['site_search_term']);
		}

		if (!empty($this->session->userdata[$this->section_name]['site_sort_by'])) {

			$this->sites_model->sort_by = $this->session->userdata[$this->section_name]['site_sort_by'];
		}

		if (!empty($this->session->userdata[$this->section_name]['site_sort_order'])) {

			$this->sites_model->sort_order = $this->session->userdata[$this->section_name]['site_sort_order'];
		}



		$sites                            = $this->sites_model->get_active_site_listing($site_id, $role_id, $region_id);

		$this->sites_model->_record_count = true;

		$total_records                    = $this->sites_model->get_active_site_listing($site_id, $role_id, $region_id);

		// Pass data to view file

		$this->search_term     = $this->sites_model->search_term;

		$data['sites']         = $sites;

		$data['page_number']   = $this->page_number;

		$data['total_records'] = $total_records;

		$data['search_term']   = $this->sites_model->search_term;

		$data['sort_by']       = $this->sites_model->sort_by;

		$data['sort_order']    = $this->sites_model->sort_order;

		$data['monthly']       = $monthly;

		$data['annual']        = $annual;

		$data['daily_trends']  = $daily_trends;



		//Create page-title

		$this->theme->set('page_title', lang('site-management'));



		//Render view

		$this->theme->view($data);
	}

	public function get_sites()
	{
		// $site_id = $this->session->userdata[$this->section_name]['site_id'];
		// $hotel_sites = $this->users_model->get_sites_list();
		// $site_info = $this->sites_model->get_site_detail_with_region_filter($site_id, $user_id, $role_id, $region_ids);
		$hotel_sites = $this->users_model->get_sites_list();
		// $user_id = $this->session->userdata[$this->section_name]['user_id'];
		// $role_id = $this->session->userdata[$this->section_name]['role_id'];

		$region_ids = $this->input->post('region_ids');
		$id = $this->input->post('id');

		$user_site_detail = $this->users_model->get_user_detail($id);
		$site_id = $user_site_detail['site_id'];

		$hotel_sites_with_region_filter = $this->sites_model->get_all_sites_with_region_filter($region_ids);

		$sites = array();
		$site_results = $this->users_model->get_site_to_user($id);
		if (!empty($site_results)) {
			foreach ($site_results as $result) {
				$sites[] = $result['site_id'];
			}
		}

		$sitesDropdown = array();
		$sitesDropdown['hotel_sites'] = $hotel_sites;
		$sitesDropdown['hotel_sites_with_region_filter'] = $hotel_sites_with_region_filter;
		$sitesDropdown['sites'] = $sites;
		$sitesDropdown['site_id'] = $site_id;
		$html = $this->get_site_dropdown($sitesDropdown);
		echo $html;
		die;
	}

	private function get_site_dropdown($data)
	{
		$label = '<label class="main-label">Site</label>';
		$siteDropdown = form_dropdown('site_id[]', $data['hotel_sites'], $data['site_id'], 'data-type="custom-dropdown" id="site_action"', ' style="width:105px;"');
		$errorElement = '<label class="input-label validation_error">' . form_error("site_id") . '</label>';
		$formDropdownContainer = '<div class="form-dropdown">' . $siteDropdown . $errorElement . '</div>';
		$basePath = base_url('themes/default/');
		$addDropdownInstance = form_dropdown('site_id[]',  $data['hotel_sites_with_region_filter'], '', 'data-type="custom-dropdown-addmore" id="site_action"', ' style="width:105px;"');
		$dropDownString = str_replace('"', "'", $addDropdownInstance);

		$dataRow = "<div class='row add-row sit_dynaminc-row'><div class='form-col-10'><div class='form-dropdown'>" . $dropDownString . "</div></div><div class='form-col-1'><button type='button' class='btn-control substract substracttoggle'><img src='" . $basePath . "images/minus-icon.png' alt='Minus'></button></div></div>";

		$plusBtn = '<div id="if_multi_site" class="form-col-1">
	<button type="button" class="btn-control addition additiondropdown" data-row="' . $dataRow . '"><img src="' . $basePath . 'images/plus-icon.png" alt="Plus"></button></div>';
		$divRow = '<div class="row"><div class="form-col-10">' . $formDropdownContainer . '</div>' . $plusBtn . '</div>';
		$html = $label . $divRow;

		if (!empty($data['sites'])) {
			foreach ($data['sites'] as $key => $value) {
				if ($key <= 0) {
					continue;
				}
				$siteHtml = '';
				$siteAddDropdownInstance = form_dropdown('site_id[]',  $data['hotel_sites'], $value, 'data-type="custom-dropdown" data-count="' . count($data["sites"]) . '" class="site_action" id="site_action_' . $key . '"', ' style="width:105px;"');
				$siteDropDownString = str_replace('"', "'", $siteAddDropdownInstance);
				$siteHtml = '<div class="row add-row sit_dynaminc-row"><div class="form-col-10">
		<div class="form-dropdown">' . $siteDropDownString . '</div></div><div class="form-col-1">
		<button type="button" class="btn-control substract substracttoggle"><img src="' . $basePath . 'images/minus-icon.png" alt="Minus"></button></div></div>';
				$html .= $siteHtml;
			}
		}
		return $html;
	}

	public function waste($siteId)
	{
		$this->theme->set('page_title', $this->lang->line('waste'));
		$this->breadcrumb->add(lang('edit-site'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $siteId . '');
		$this->breadcrumb->add($this->lang->line('waste'));

		$tabData  = getWasteTabData();

		$user_id = $this->session->userdata[$this->section_name]['user_id'];

		$this->load->model('sites/site_waste_model');
		$this->site_waste_model->site_id = $siteId;
		$this->site_waste_model->user_id = $this->user_id;
		$this->site_waste_model->year_id = NULL;
		$this->site_waste_model->month_id = NULL;
		$this->site_waste_model->rebates = NULL;

		$site_waste_result = $this->site_waste_model->get_site_waste_model_detail_by_siteId_userId();
		$site_waste = (isset($site_waste_result[0]['s']) && !empty($site_waste_result[0]['s'])) ? $site_waste_result[0]['s'] : [];

		if ($this->input->post()) {
			$postData = $this->input->post();
			if (!empty($postData['wasteFormSubmit'])) {
				$wasteStreams = array(
					'bottles_cans',
					'wastetoenergy',
					'cardboard',
					'paper',
					'mixed_glass',
					'alluminium',
					'pete_plastic_bottles',
					'hdpe',
					'other_plastics',
					'bottled_amenities',
					'soap_bars',
					'palettes_and_crates',
					'e_waste',
					'durable_goods',
					'solid_food_waste',
					'leftover_food',
					'inedible_parts',
					'liquid_food_waste',
					'kitchen_grease',
					'liquid_hazardous_waste',
					'other_hazardous_waste',
					'batteries',
					'light_bulbs',
					'light_fixtures',
					'textiles',
					'wood',
					'building_constructions',
					'other',
					'recycling',
					'commingled_recyclables',
					'paper_cardboard',
					'mixed_metals',
					'plastics',
					'donations',
					'toiletry_donations',
					'biodegradable',
					'mixed_organic',
					'food_waste',
					'landfill_other',
					'hazardous_waste',
					'universal_waste',
					'other_materials',
					'hazardous_and_universal_waste',
					'medical_waste',
					'tin',
				);

				foreach ($wasteStreams as $stream) {
					$destKey = 'typical_destination_' . $stream;
					$unitKey = 'unit_measure_dropdown_' . $stream;
					$sourceKey = 'source_' . $stream;
					$trackKey = 'monthly_tracking_' . $stream;
					$checkKey = 'is_check_' . $stream;

					$existingDest = isset($site_waste[$destKey]) ? $site_waste[$destKey] : 0;
					$existingUnit = isset($site_waste[$unitKey]) ? $site_waste[$unitKey] : 0;
					$existingSource = isset($site_waste[$sourceKey]) ? $site_waste[$sourceKey] : 0;
					$existingTrack = isset($site_waste[$trackKey]) ? $site_waste[$trackKey] : 0;
					$existingCheck = isset($site_waste[$checkKey]) ? $site_waste[$checkKey] : 0;

					$this->site_waste_model->$destKey = array_key_exists($destKey, $postData) ? $postData[$destKey] : $existingDest;
					$this->site_waste_model->$unitKey = array_key_exists($unitKey, $postData) ? $postData[$unitKey] : $existingUnit;
					if (array_key_exists($sourceKey, $postData)) {
						$this->site_waste_model->$sourceKey = is_array($postData[$sourceKey]) ? implode(',', $postData[$sourceKey]) : $postData[$sourceKey];
					} else {
						$this->site_waste_model->$sourceKey = $existingSource;
					}
					$this->site_waste_model->$trackKey = array_key_exists($trackKey, $postData) ? $postData[$trackKey] : $existingTrack;

					// Keep monthly totals/costs; they are not part of this settings form.
					$measureKey = 'unit_measure_' . $stream;
					$costKey = 'disposal_cost_' . $stream;
					$totalKey = 'total_' . $stream;
					if (isset($site_waste[$measureKey])) {
						$this->site_waste_model->$measureKey = $site_waste[$measureKey];
					}
					if (isset($site_waste[$costKey])) {
						$this->site_waste_model->$costKey = $site_waste[$costKey];
					}
					if (isset($site_waste[$totalKey])) {
						$this->site_waste_model->$totalKey = $site_waste[$totalKey];
					}

					$newCheck = array_key_exists($checkKey, $postData) ? $postData[$checkKey] : $existingCheck;
					$this->site_waste_model->$checkKey = $newCheck;
					if (!empty($existingCheck) && empty($newCheck)) {
						$this->site_waste_model->update_untracked_record($siteId, $stream);
					}
				}

				if (isset($site_waste['rebates'])) {
					$this->site_waste_model->rebates = $site_waste['rebates'];
				}

				$this->site_waste_model->insert_site_waste();

				if (isset($site_waste) && !empty($site_waste)) {
					$this->theme->set_message(lang('msg_update_success'), 'success');
				} else {
					$this->theme->set_message(lang('msg_add_success'), 'success');
				}
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $siteId);
			}
		}

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];

		//Variable assignments to view
		$data = array();
		$data['csrf_token'] = $this->security->get_csrf_token_name();
		$data['csrf_hash'] = $this->security->get_csrf_hash();
		$data['tab_data'] = isset($tabData) ?  $tabData : [];
		$data['site_waste'] = isset($site_waste) ?  $site_waste : [];
		$data['site_detail'] = $this->sites_model->get_site_detail($siteId, $user_id, $role_id);
		$data_action = 'Update';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $siteId, 'Site Waste Settings', $data_action);
		//Render view
		$this->theme->view($data, 'admin_waste');
	}

	public function emission($site_id)
	{
		$this->theme->set('page_title', $this->lang->line('emission'));
		$this->breadcrumb->add(lang('edit-site'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $site_id . '');
		$this->breadcrumb->add($this->lang->line('emission'));

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$year  = date('Y');

		if ($this->input->get('year')) {
			$year = $this->input->get('year');
		}

		if ($this->input->post()) {
			$postData = $this->input->post();

			if (isset($postData['year'])) {
				$year = $postData['year'];
			}

			if ($_POST['emissionFormSubmit']) {
				$this->load->model('sites/site_emission_model');
				$this->site_emission_model->site_id = $site_id;
				// $this->site_emission_model->user_id  = $user_id;
				$this->site_emission_model->year_id  = $year;
				$this->site_emission_model->electricity_emission_factor_percentage = isset($postData['electricity_emission_factor_percentage']) ? $postData['electricity_emission_factor_percentage'] : NULL;
				$this->site_emission_model->electricity_emission_factor = isset($postData['electricity_emission_factor']) ? $postData['electricity_emission_factor'] : NULL;
				$this->site_emission_model->fuel_emission_factor = isset($postData['fuel_emission_factor']) ? $postData['fuel_emission_factor'] : NULL;
				$this->site_emission_model->lpg_emission_factor = isset($postData['lpg_emission_factor']) ? $postData['lpg_emission_factor'] : NULL;
				$this->site_emission_model->natural_gas_emission_factor = isset($postData['natural_gas_emission_factor']) ? $postData['natural_gas_emission_factor'] : NULL;
				$this->site_emission_model->district_cooling_emission_factor = isset($postData['district_cooling_emission_factor']) ? $postData['district_cooling_emission_factor'] : NULL;
				$this->site_emission_model->district_heating_emission_factor = isset($postData['district_heating_emission_factor']) ? $postData['district_heating_emission_factor'] : NULL;
				$this->site_emission_model->waste_general_waste_to_landfill_emission_factor = isset($postData['waste_general_waste_to_landfill_emission_factor']) ? $postData['waste_general_waste_to_landfill_emission_factor'] : NULL;
				$this->site_emission_model->waste_recycling_streams_emission_factor = isset($postData['waste_recycling_streams_emission_factor']) ? $postData['waste_recycling_streams_emission_factor'] : NULL;
				$this->site_emission_model->business_travel_flights_emission_factor = isset($postData['business_travel_flights_emission_factor']) ? $postData['business_travel_flights_emission_factor'] : NULL;
				$this->site_emission_model->business_travel_car_taxi_emission_factor = isset($postData['business_travel_car_taxi_emission_factor']) ? $postData['business_travel_car_taxi_emission_factor'] : NULL;
				$this->site_emission_model->employee_commuting_car_emission_factor = isset($postData['employee_commuting_car_emission_factor']) ? $postData['employee_commuting_car_emission_factor'] : NULL;
				$this->site_emission_model->employee_commuting_bus_emission_factor = isset($postData['employee_commuting_bus_emission_factor']) ? $postData['employee_commuting_bus_emission_factor'] : NULL;
				$this->site_emission_model->outsourced_laundry_emission_factor = isset($postData['outsourced_laundry_emission_factor']) ? $postData['outsourced_laundry_emission_factor'] : NULL;
				$this->site_emission_model->purchased_goods_emission_factor = isset($postData['purchased_goods_emission_factor']) ? $postData['purchased_goods_emission_factor'] : NULL;
				$this->site_emission_model->status = 1;

				if (isset($_FILES) && !empty($_FILES)) {
					foreach ($_FILES as $file_key => $file_value) {
						$cpt = count($file_value['name']);
						for ($index = 0; $index < $cpt; $index++) {
							if (isset($file_value['name'][$index]) && !empty($file_value['name'][$index])) {
								$imagename = $file_value['name'][$index];
								$tempimagename = $file_value['tmp_name'][$index];
								$size = $file_value['size'][$index];
								$i = strrpos($imagename, ".");
								if (!$i) {
									$ext = '';
								}
								$l = strlen($imagename) - $i;
								$ext = substr($imagename, $i + 1, $l);
								$emission_file_image_name = 'emission_file_' . rand(11111, 9999999) . '.' . $ext;
								if ($ext) {
									// procedure further if and only if image size can not be more than 10MB.
									if ($size < (1024 * 1024 * 10)) {
										$uploadedfile = $tempimagename;
										$target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $emission_file_image_name;
										$_movestatus = move_uploaded_file($uploadedfile, $target_file);
										if (!$_movestatus) {
											$this->theme->set_message('Todo image is not uploaded', 'error');
										} else {
											$emission_image = trim(strip_tags($emission_file_image_name));
											$emissionUpload[$index] = $emission_image;
										}
									} else {
										$this->theme->set_message('site image size is too large', 'error');
										redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/emission/' . $site_id);
										exit;
									}
								}
							}
							$this->site_emission_model->emission_upload = isset($emissionUpload) && !empty($emissionUpload) ? implode("|", $emissionUpload) : '';
						}
					}
				}
				$this->site_emission_model->insert_site_emission();
				$this->theme->set_message(lang('msg_add_success'), 'success');
				$data_action = 'Import';
				$site_id = $_SESSION['admin']['site_id'];
				$user_id = $_SESSION['admin']['user_id'];
				saveAuditTrail($user_id, $site_id, 'Import Emissions', $data_action);
				redirect(BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $site_id);
			}

			if (!empty($postData['wasteEmissionFactorsSubmit'])) {
				$this->load->model('sites/site_waste_scope_1_2_emission_factor_model');
				$factorRows = isset($postData['waste_ef']) ? $postData['waste_ef'] : [];
				$factorValues = [];
				foreach ($factorRows as $masterFactorId => $factorRow) {
					$epaFactor = (isset($factorRow['epa_source_factor']) && is_numeric($factorRow['epa_source_factor']))
						? round((float) $factorRow['epa_source_factor'], 4)
						: null;
					$hepFactor = (isset($factorRow['hep_factor']) && is_numeric($factorRow['hep_factor']))
						? round((float) $factorRow['hep_factor'], 2)
						: null;
					if ($epaFactor !== null && $hepFactor === null) {
						$hepFactor = round(convertWasteEmissionFactor($epaFactor, 'tco2e_short_ton'), 2);
					} elseif ($hepFactor !== null && $epaFactor === null) {
						$epaFactor = round(convertWasteEmissionFactor($hepFactor, 'kgco2e_mt'), 4);
					}
					$factorValues[(int) $masterFactorId] = [
						'epa_source_factor' => $epaFactor,
						'hep_factor' => $hepFactor,
					];
				}
				$this->site_waste_scope_1_2_emission_factor_model->saveSiteYearValues($site_id, $year, $factorValues);
				saveAuditTrail($user_id, $site_id, 'Scope 3 Waste Emission Factors', 'Update');
				$this->theme->set_message('Waste emission factors saved successfully.', 'success');
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/emission/' . $site_id . '?year=' . $year);
			}
		}

		$this->load->model('sites/site_emission_model');
		$this->site_emission_model->site_id = $site_id;
		$this->site_emission_model->user_id = $user_id;
		$this->site_emission_model->year_id  = $year;
		$site_emission_result = $this->site_emission_model->get_site_emission_model_detail_by_siteId();
		$site_emission = isset($site_emission_result) && !empty($site_emission_result) ? $site_emission_result[0]['s'] : [];

		$this->load->model('sites/site_waste_scope_1_2_emission_factor_model');
		$waste_emission_factors = $this->site_waste_scope_1_2_emission_factor_model->getRowsWithValues($year, $site_id);

		//Variable assignments to view
		$data = array();
		$data['csrf_token'] = $this->security->get_csrf_token_name();
		$data['csrf_hash'] = $this->security->get_csrf_hash();
		$data['site_emission'] = isset($site_emission) && !empty($site_emission) ? $site_emission : [];
		$data['waste_emission_factors'] = $waste_emission_factors;
		$data['utilities_year']  = $year;
		$data['site_id']  = $this->session->userdata[$this->section_name]['site_id'];
		$data['site_param_id']  = $site_id;
		$data['site_name'] = $this->sites_model->get_site_detail($site_id, $user_id, $role_id)['site_location_name'];
		//Render view
		$this->theme->view($data, 'admin_emission');
	}

	// delete_emission_image
	function delete_emission_image()
	{
		$siteEmissionId = intval($this->input->post('siteEmissionId'));
		$imageName = $this->input->post('imageName');

		//logic
		if ($siteEmissionId != 0 && $siteEmissionId != '' && is_numeric($siteEmissionId)) {
			$user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
			$data['deleted_at'] = GetCurrentDateTime();
			$data['deleted_by'] = $user_id;
			$data['site_emission_id'] = $siteEmissionId;
			$data['emission_upload'] = $imageName;
			if ($this->site_emission_model->delete_emission_image($data)) {
				$message = $this->theme->message(lang('msg_delete_success'), 'success');
			} else {
				$message = $this->theme->message(lang('invalid_id_msg'), 'error');
			}
		} else {
			$message = $this->theme->message(lang('invalid_id_msg'), 'error');
		}
		$data_action = 'Delete';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Delete Emission Image', $data_action);
		echo $message;
	}

	public function view_area_history($site_id)
	{
		$this->theme->set('page_title', $this->lang->line('area_update_history'));
		$this->breadcrumb->add(lang('edit-site'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $site_id . '');
		$this->breadcrumb->add($this->lang->line('area_update_history'));

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$site_area_update_history = $this->sites_model->get_site_area_update_history($site_id);
		//Variable assignments to view
		$data = array();
		$data['csrf_token'] = $this->security->get_csrf_token_name();
		$data['csrf_hash'] = $this->security->get_csrf_hash();
		$data['site_area_update_history'] = isset($site_area_update_history) ?  $site_area_update_history : [];
		$data['site_detail'] = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);
		//Render view
		$this->theme->view($data, 'admin_view_area_update_history');
	}

	public function residence($site_id)
	{
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$sitedetail = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);
		$residence_types = isset($sitedetail['residence_types']) ? explode(',', $sitedetail['residence_types']) : [];
		if (in_array(RENTAL_PROGRAM_RESIDENCE, $residence_types) || in_array(PRIVATE_RESIDENCE, $residence_types)) {
			$this->theme->set('page_title', $this->lang->line('residence'));
			$this->breadcrumb->add(lang('edit-site'), base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $site_id . '');
			$this->breadcrumb->add($this->lang->line('residence'));

			$year  = date('Y');
			if ($this->input->post()) {
				$postData = $this->input->post();
				if (isset($postData['year'])) {
					$year = $postData['year'];
				}

				if ($_POST['residenceFormSubmit']) {
					$this->load->model('sites/site_residence_model');
					$this->site_residence_model->site_id = $site_id;
					$this->site_residence_model->user_id  = $user_id;
					$this->site_residence_model->year_id  = $year;
					$this->site_residence_model->utility_type = isset($postData['utility_type']) ? $postData['utility_type'] : NULL;
					$this->site_residence_model->private_program_consumption = isset($postData['private_program_consumption']) ? (int) $postData['private_program_consumption'] : NULL;
					$this->site_residence_model->rental_program_residence_consumption = isset($postData['rental_program_residence_consumption']) ? (int) $postData['rental_program_residence_consumption'] : NULL;
					if (isset($postData['private_program_consumption']) && !empty($postData['private_program_consumption']) && $postData['private_program_consumption'] == 1) {
						$this->site_residence_model->private_program_hotel_connected = isset($postData['private_program_hotel_connected']) ? (int) $postData['private_program_hotel_connected'] : NULL;
					}
					if (isset($postData['private_program_consumption']) && !empty($postData['private_program_consumption'])) {
						if ($postData['private_program_consumption'] == 2) {
							$this->site_residence_model->private_program_float = $this->site_residence_model->calculateFloatPercentage($sitedetail, PRIVATE_RESIDENCE); //isset($postData['private_program_float']) ? (float)$postData['private_program_float'] : NULL;
						}
						if ($postData['private_program_consumption'] == 3) {
							$this->site_residence_model->private_program_fixed = isset($postData['private_program_fixed']) ? (float)$postData['private_program_fixed'] : NULL;
						}
					}
					if (isset($postData['rental_program_residence_consumption']) && !empty($postData['rental_program_residence_consumption']) && $postData['rental_program_residence_consumption'] == 1) {
						$this->site_residence_model->rental_program_residence_hotel_connected = isset($postData['rental_program_residence_hotel_connected']) ? (int) $postData['rental_program_residence_hotel_connected'] : NULL;
					}
					if (isset($postData['rental_program_residence_consumption']) && !empty($postData['rental_program_residence_consumption'])) {
						if ($postData['rental_program_residence_consumption'] == 2) {
							$this->site_residence_model->rental_program_residence_float = $this->site_residence_model->calculateFloatPercentage($sitedetail, RENTAL_PROGRAM_RESIDENCE); //isset($postData['rental_program_residence_float']) ? (float)$postData['rental_program_residence_float'] : NULL;
						}
						if ($postData['rental_program_residence_consumption'] == 3) {
							$this->site_residence_model->rental_program_residence_fixed = isset($postData['rental_program_residence_fixed']) ? (float)$postData['rental_program_residence_fixed'] : NULL;
						}
					}
					if (!empty($year) && !empty($this->site_residence_model->utility_type) && !empty($this->site_residence_model->user_id) && !empty($this->site_residence_model->site_id)) {
						$this->site_residence_model->insert_site_residence();
						$this->theme->set_message(lang('msg_add_success'), 'success');
					}
					redirect(base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit/' . $site_id);
				}
			}

			$this->load->model('sites/site_residence_model');
			$this->site_residence_model->site_id = $site_id;
			$this->site_residence_model->user_id = $user_id;
			$this->site_residence_model->year_id  = $year;
			$this->site_residence_model->private_program_consumption  = NULL;
			$this->site_residence_model->rental_program_residence_consumption  = NULL;
			$this->site_residence_model->utility_type  = isset($postData['utility_type']) ? $postData['utility_type'] : NULL;
			$site_residence_result = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
			$site_residence = isset($site_residence_result) && !empty($site_residence_result) ? $site_residence_result[0]['s'] : [];

			//Variable assignments to view
			$data = array();
			$data['csrf_token'] = $this->security->get_csrf_token_name();
			$data['csrf_hash'] = $this->security->get_csrf_hash();
			$data['site_residence'] = isset($site_residence) && !empty($site_residence) ? $site_residence : [];
			$data['site_id']  = $site_id;
			$data['residence_types']  = $residence_types;
			$data['utility_type'] = isset($this->site_residence_model->utility_type) ? $this->site_residence_model->utility_type : '';
			$data['year'] = $year;
			$data['site_detail'] = $sitedetail;

			$this->site_residence_model->utility_type  = NULL;
			$this->site_residence_model->private_program_consumption  = NULL;
			$this->site_residence_model->rental_program_residence_consumption  = NULL;
			$site_residence_list = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
			$site_residence_list = isset($site_residence_list) && !empty($site_residence_list) ? $site_residence_list : [];
			foreach ($site_residence_list as $key => $residence) {
				if ($sitedetail['show_utility_' . $residence['utility_type']] == 0) {
					unset($site_residence_list[$key]);
				}
			}
			$data['site_residence_list'] = $site_residence_list;
			$data_action = 'Create';
			$site_id = $_SESSION['admin']['site_id'];
			$user_id = $_SESSION['admin']['user_id'];
			saveAuditTrail($user_id, $site_id, 'Import Residence', $data_action);
			$this->theme->view($data, 'admin_residence');
		} else {
			$this->theme->set_message(lang('permission-not-allowed'), 'error');
			redirect(base_url() . BASE_ADMIN_URL_CUSTOM . 'sites/edit' . $site_id);
			exit;
		}
	}


	public function export_waste()
	{
		$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$tabData = getWasteTabData();
		$row1Headings = $row2Headings = [];
		// 1st and 2nd row TabData Heading Array
		foreach ($tabData as $keyRow1 => $valueRow1) {
			if (strpos($keyRow1, ' #') !== false) {
				$arr = explode(" #", $keyRow1, 2);
				$row1Heading = $arr[0];
			} else {
				$row1Heading = $keyRow1;
			}
			$row1Name = isset($keyRow1) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($keyRow1)))))) : '';
			if (strpos($row1Name, '_#') !== false) {
				$data = explode('_#', $row1Name);
				$row1Name = isset($data[2]) ? $data[2] : $data[0];
			}
			$row1Headings[$row1Name] = trim($row1Heading);
			if (!isset($valueRow1) && empty($valueRow1)) {
				$row2Headings[$row1Name]['label'] = trim($row1Heading);
				$row2Headings[$row1Name]['color'] = 'BFBFBF';
			} else {
				$row2Headings[$row1Name]['label'] = trim($row1Heading);
				$row2Headings[$row1Name]['color'] = 'BFBFBF';
				foreach ($valueRow1 as $keyRow2 => $valueRow2) {
					if (strpos($keyRow2, ' #') !== false) {
						$arr2 = explode(" #", $keyRow2, 2);
						$row2Heading = $arr2[0];
					} else {
						$row2Heading = $keyRow2;
					}
					$row2Name = isset($keyRow2) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($keyRow2)))))) : '';
					if (strpos($row2Name, '_#') !== false) {
						$data = explode('_#', $row2Name);
						$row2Name = isset($data[2]) ? $data[2] : $data[0];
					}
					$row2Headings[$row2Name]['label'] = trim($row2Heading);
					$row2Headings[$row2Name]['color'] = 'D9D9D9';
					if (isset($valueRow2) && !empty($valueRow2)) {
						foreach ($valueRow2 as $keySubChild => $valueSubChild) {
							$nameKey = isset($valueSubChild['name']) ? $valueSubChild['name'] : $valueSubChild['label'];
							$row2Name = isset($nameKey) ? str_replace(' ', '_', str_replace(' & ', ' ', str_replace('(', '', str_replace(')', '', str_replace('/', ' ',   strtolower($nameKey)))))) : '';
							if (strpos($row2Name, '_#') !== false) {
								$data = explode('_#', $row2Name);
								$row2Name = isset($data[2]) ? $data[2] : $data[0];
							}
							$row2Headings[$row2Name]['label'] = trim($valueSubChild['label']);
							$row2Headings[$row2Name]['color'] = 'F2F2F2';
						}
					}
				}
			}
		}

		
		$typical_destinations = getWasteTypicalDestinationArray(); 
		$sources = getWasteSourceArray();
		$monthly_trackings = getWasteMonthlyTrackingArray();
		$unit_measures = getWasteUnitMeasuresArray();
		$row1HeadingColors = [
			'F8CBAD',
			'C7B19B',
			'D9E1F2',
			'E2EFDA',
			'E8D1FF',
			'FFDC79',
			'FEC2F7'
		];
		$Heading1MergeIndex = [
			'H1:H1',
			'I1:I1',
			'J1:W1',
			'X1:AE1',
			'AF1:AL1',
			'AM1:AU1',
			'AV1:AZ1'
		];
		$wroksheetTitles = [
			'Waste Amount',
			'Waste Details',
			'Waste Destination',
			'Waste Source',
		];
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");

		//Start adding next sheets
		for ($index = 0; $index < 4; $index++) {
			$columns = $sites = $siteWasteUtilityData = [];
			// Add new sheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($index);
			$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
			$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords($wroksheetTitles[$index]);
			$objPHPExcel->getActiveSheet()->setTitle($wroksheetTitles[$index]);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
			if($index == 0){
				$fixedColumns = ['A1:H1','A2:H2'];
			}
			else
			{
				$fixedColumns = ['A1:G1','A2:G2'];
			}
			$objPHPExcel->getActiveSheet()->getStyle($fixedColumns[0])->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FFF2CC')
					)
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle($fixedColumns[1])->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => 'FFF2CC')
					)
				)
			);
			$objPHPExcel->getActiveSheet()->setAutoFilter($fixedColumns[1]);
			$this->load->model('sites/site_waste_model');
			$sites = $this->site_waste_model->getAllSiteRegionWasteData();
			$columns["attribute"] = 'Attribute';
			$columns["property_name"] = 'Property Name';
			$columns["country"] = 'Country';
			$columns["region"] = 'Region';
			$columns["property_type"] = 'Property Type';
			if ($index == 0) {
				$columns["month_id"] = 'Reporting Month';
				$columns["year_id"] = 'Reporting Year';
				$columns["rebates"] = 'Rebates';
			} else {
				$columns["last_update_by"] = 'Last update by';
				$columns["last_update_date"] = 'Last update date';
			}
			foreach ($row2Headings as $key => $value) {
				if ($index == 0) {
					$key = 'unit_measure_dropdown_' . $key;
				} else if ($index == 1) {
					$key = 'monthly_tracking_' . $key;
				} else if ($index == 2) {
					$key = 'typical_destination_' . $key;
				} else if ($index == 3) {
					$key = 'source_' . $key;
				}
				$row2Headings[$key]['color'] = $value['color'];
				$row2Headings[$key]['label'] = $value['label'];
				if (!isset($columns[$key]) && empty($column[$key])) {
					$columns[$key] = $value['label'];
				}
			}
			$indexColor = 0;
			// Row 1 to display tabData 6 main heading
			// If index is 0 then add rebate column
			if($index == 0){
				$Heading1MergeIndexDynamic = [
					'I1:I1',
					'J1:J1',
					'K1:X1',
					'Y1:AF1',
					'AG1:AM1',
					'AN1:AV1',
					'AW1:BB1'
				];
			}
			else
			{
				$Heading1MergeIndexDynamic = $Heading1MergeIndex;
			}
			foreach ($row1Headings as $key => $column) {
				$objPHPExcel->getActiveSheet()->mergeCells($Heading1MergeIndexDynamic[$indexColor]);
				$objPHPExcel->getActiveSheet()->getStyle($Heading1MergeIndexDynamic[$indexColor])->getFill()->applyFromArray(array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor' => array(
						'rgb' => $row1HeadingColors[$indexColor]
					)
				));
				$arr = explode(":", $Heading1MergeIndexDynamic[$indexColor], 2);
				$cellCord = $arr[0];
				$objPHPExcel->getActiveSheet()->setCellValue($cellCord, $column);
				$indexColor++;
			}

			// Row 2 to display all cell headings sites, tabData main heading, subheading and childHeadings
			$cells = array();
			$later1 = "";
			$later2 = 'A';
			$flag = 0;
			foreach ($columns as $key => $column) {
				if ($later1 . $later2 == 'BB') {
					break;
				} else {
					if (isset($row2Headings[$key]) && !empty($row2Headings[$key])) {
						$objPHPExcel->getActiveSheet()->getStyle($later1 . $later2 . "2")->getFill()->applyFromArray(array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'startcolor' => array(
								'rgb' => $row2Headings[$key]['color']
							)
						));
					}

					$objPHPExcel->getActiveSheet()->setCellValue($later1 . $later2 . "2", $column);
					$cells[$key] = $later1 . $later2;
					$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
					if ($later2 == 'Z') {
						if ($flag == 0) {
							$later1 = 'A';
							$flag = 1;
						} else {
							$later1++;
						}
						$later2 = 'A';
					} else {
						$later2++;
					}
				}
			}

			// Display data from row 3 for each site
			$row = 3;
			if ($index == 0) {
				$describingLabel = 'Amount - Unit';
			} else if ($index == 1) {
				$describingLabel = 'Tracked /Not Tracked';
			} else if ($index == 2) {
				$describingLabel = 'destination options';
			} else if ($index == 3) {
				$describingLabel = 'source options';
			}
			$later1Row3 = "";
			$later2Row3 = 'H';
			$flagRow3 = 0;
			foreach ($columns as $column) {
				if ($later1Row3 . $later2Row3 == 'BB') {
					break;
				} else {
					$objPHPExcel->setActiveSheetIndex($index)->setCellValue($later1Row3 . $later2Row3 . "3", $describingLabel);
					$cells[$key] = $later1Row3 . $later2Row3;
					if ($later1Row3 == 'Z') {
						if ($flagRow3 == 0) {
							$later1Row3 = 'A';
							$flagRow3 = 1;
						} else {
							$later1Row3++;
						}
						$later2Row3 = 'A';
					} else {
						$later2Row3++;
					}
					$phpColor = new PHPExcel_Style_Color();
					$phpColor->setRGB('808080');
					$objPHPExcel->getActiveSheet()->getStyle('A3:BA3')->getFont()->setColor($phpColor);
				}
			}

			// Set audit log values for each site
			if ($index !== 0) {
				foreach ($sites as $key => $value) {
					$audit_detail = $this->site_waste_model->getLatestAuditWasteDetail($value['id']);
					$sites[$key]['last_update_date'] = $audit_detail['last_update_date'];
					$sites[$key]['last_update_by'] = $audit_detail['last_update_by'];
				}
			} else {
				// Waste Amount: one row per (site, month, year).
				$expandedSites = [];
				foreach ($sites as $wasteSetting) {
					$siteWasteUtility = $this->site_waste_model->get_site_waste_utility_data($wasteSetting['id']);
					foreach ($siteWasteUtility as $wasteData) {
						$monthId = (int) $wasteData['month_id'];
						$yearId = (int) $wasteData['year_id'];
						if ($monthId <= 12 && $monthId >= 1 && $yearId >= 2016) {
							$uniqueKey = $wasteSetting['id'] . '_' . $monthId . '_' . $yearId;
							$row = $wasteSetting;
							$row['year_id'] = $yearId;
							$row['month_id'] = $monthId;
							$expandedSites[$uniqueKey] = $row;
							$siteWasteUtilityData[$wasteData['site_id']][$monthId][$yearId] =
								(!empty($wasteData)) ? $wasteData : '';
						}
					}
				}
				$sites = array_values($expandedSites);
			}

			// Display data from row 4 for each site
			$row = 4;
			foreach ($sites as $keyMain => $data) {
				foreach ($data as $key => $val) {
					if (array_key_exists($key, $cells)) {
						if ((substr($key, 0, 22) == "unit_measure_dropdown_") || (substr($key, 0, 17) == "monthly_tracking_") || (substr($key, 0, 20) == "typical_destination_") || (substr($key, 0, 7) == "source_")) {
							if (empty($val) || $val == 0 || !isset($val)) {
								$val = 'NA';
							} else {
								if ($index == 0) {
									$unit_measure_key = trim(str_replace('dropdown_', '', $key));
									$keyName = trim(str_replace('unit_measure_dropdown_', '', $key));
									if ($sites[$keyMain]['monthly_tracking_' . $keyName] == 1) {
										$val = 'NA';
									} else {
										$amount = $siteWasteUtilityData[$sites[$keyMain]['id']][$sites[$keyMain]['month_id']][$sites[$keyMain]['year_id']][$unit_measure_key];
										$val = (isset($amount) && !empty($amount) && ($amount != 0)) ? $amount . '-' . html_entity_decode($unit_measures[$val]) : '';
									}
								} else if ($index == 1) {
									$val = ($val == 1) ? 'Not Tracked' : 'Tracked';
								} else if ($index == 2) {
									$val = $typical_destinations[$val];
								} else if ($index == 3) {
									if (strpos($val, ',') !== false) {
										$SourceValues = explode(",", $val);
										$labelledSource = [];
										foreach ($SourceValues as $keysource => $valuesource) {
											array_push($labelledSource, $sources[$valuesource]);
										}
										$val = implode(',', $labelledSource);
									} else {
										$val = $sources[$val];
									}
								}
							}
						}
						$objPHPExcel->setActiveSheetIndex($index);
						$objPHPExcel->getActiveSheet()->setCellValue($cells[$key] . $row, $val);
					}
				}
				$row++;
			}
		}

		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Waste', $data_action);
		ob_end_clean();
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Group Waste Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	public function replicate_residence($site_id)
	{
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$this->load->model('sites/site_residence_model');
		$this->site_residence_model->site_id = $site_id;
		$this->site_residence_model->year_id  = date('Y') - 1;
		$this->site_residence_model->utility_type  = NULL;
		$site_residence_result = $this->site_residence_model->get_site_residence_model_detail_by_siteId();
		if (isset($site_residence_result) && !empty($site_residence_result)) {
			foreach ($site_residence_result as $key => $value) {
				foreach ($value as $keyObject => $valueObject) {
					$this->load->model('sites/site_residence_model');
					if ($keyObject != 'year_id') {
						$this->site_residence_model->{$keyObject} = $valueObject;
					} else {
						$this->site_residence_model->year_id = date('Y');
					}
				}
				$this->site_residence_model->insert_site_residence();
				$this->theme->set_message("Data replicated successfully.", 'success');
				$data_action = 'Update';
				$site_id = $_SESSION['admin']['site_id'];
				$user_id = $_SESSION['admin']['user_id'];
				saveAuditTrail($user_id, $site_id, 'Replicate Residence', $data_action);
				redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/residence/' . $site_id);
				exit;
			}
		} else {
			$this->theme->set_message("Past year data not found.", 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'sites/residence/' . $site_id);
			exit;
		}
	}

	public function export_utility()
	{
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		ini_set('memory_limit', '-1');

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");
		//Start adding next sheets
		$columns = $sites = $siteUtilityData = [];
		// Add new sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Export Utility');
		$objPHPExcel->getActiveSheet()->setTitle('Export Utility');
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);

		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$objPHPExcel->getDefaultStyle()->applyFromArray($style);

		$colorIndexCodes = [
			'A1:D1' => 'FFF2CC',
			'E1:E1' => 'A5A5A5',
			'F1:J1' => 'D8E4BC',
			'K1:L1' => 'F2DCDB',
			'M1:N1' => 'CCC0DA',
			'O1:P1' => 'C5D9F1',
			'Q1:R1' => '4F81BD',
			'S1:U1' => 'EEECE1',
			'V1:X1' => 'FABF8F',
		];
		foreach ($colorIndexCodes as $key => $value) {
			$objPHPExcel->getActiveSheet()->getStyle($key)->applyFromArray(
				array(
					'fill' => array(
						'type' => PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => $value)
					)
				)
			);
		}
		$areaUnit = [
			'm&#178;',
			'ft&#178;'
		];
		$columns["site_location_name"] = 'Site Name';
		$columns["attribute"] = 'Attribute';
		$columns["month_id"] = 'Reporting Month';
		$columns["year_id"] = 'Reporting Year';
		$columns["local_unit"] = 'Local Area Unit';
		$columns["electricity_unit"] = 'Electricity: unit';
		$columns['total_purchased_electricity'] = 'Electricity: Utility Purchased (1)';
		$columns["total_renewable_energy_production"] = 'Electricity: Total Renewable Energy Production (2)';
		$columns["onsite_generators_quantity"] = 'Electricity: Onsite Generators Production (3)';
		$columns["total_renewable_energy_exported"] = 'Electricity: Total Renewable Energy Exported (4)';
		$columns["total_electricity_kwh"] = 'Electricity: Total Consumption (1+2+3)';
		$columns["lpg_unit"] = 'LPG: unit';
		$columns["total_lpg"] = 'LPG: Total Consumption';
		$columns["district_heating_unit"] = 'District Heating Energy: unit';
		$columns["district_heating"] = 'District Heating Energy: Total Consumption';
		$columns["district_cooling_unit"] = 'District Cooling Energy: unit';
		$columns["district_cooling"] = 'District Cooling Energy: Total Consumption';
		$columns["water_unit"] = 'Water: unit';
		$columns["water_total_consumption"] = 'Water: Total Consumption';
		$columns["fuel_oil_unit"] = 'Fuel Oil: unit';
		$columns["onsite_generators_fuel_oil_quantity"] = 'Onsite Generators Fuel Oil Consumption (4)';
		$columns["total_fuel_oil"] = 'Fuel Oil: Total Consumption including (4)';
		$columns["natural_gas_unit"] = 'Natural Gas: unit';
		$columns["onsite_generators_natural_gas_quantity"] = 'Onsite Generators Natural Gas Consumption (5)';
		$columns["total_natural_gas"] = 'Natural Gas: Total Consumption including (5)';
		$columns["total_room_night"] = 'Room Nights';
		$columns["total_room_night_budget"] = 'Room Nights Budget';
		$columns["total_guests"] = 'Total Guests';
		$columns["total_guests_budget"] = 'Total Guests Budget';
		$decimal_places = 2;
		$this->load->model('sites/sites_model');
		$UtilityData = $this->sites_model->getAllUtility();

		//remove duplicate utility record
		foreach ($UtilityData as $key => $utl) {
			if (!isset($siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']]) && empty($siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']])) {
				$siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']] = $utl;
				$UtilityData[$key] = $utl;
			} else {
				unset($UtilityData[$key]);
			}
		}

		//adding purchased electricity records to utility record
		foreach ($UtilityData as $key => $utl) {
			$current_utility = $utl;
			$this->load->model('utilities/utilities_model');
			$this->utilities_model->utilities_month = $utl['month_id'];
			$this->utilities_model->utilities_year = $utl['year_id'];
			$this->utilities_model->site_id = $utl['site_id'];
			$electricityTariff = $this->utilities_model->getElectricityTariff();

			$temp = 0;
			foreach ($electricityTariff as $single) {
				if ($temp == 0) {
					$current_utility['tariff'] = round($single['tariff'], $decimal_places);
					$current_utility['total_kwh'] = round($single['total_kwh'], $decimal_places);
				} else {
					$current_utility['tariff' . $temp] = round($single['tariff'], $decimal_places);
					$current_utility['total_kwh' . $temp] = round($single['total_kwh'], $decimal_places);
				}
				$temp++;
			}
			$UtilityData[$key] = $current_utility;
		}

		$cells = array();
		$later1 = "";
		$later2 = 'A';
		$flag = 0;
		foreach ($columns as $key => $column) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
			$cells[$key] = $later1 . $later2;
			$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
			if ($later2 == 'Z') {
				if ($flag == 0) {
					$later1 = 'A';
					$flag = 1;
				} else {
					$later1++;
				}
				$later2 = 'A';
			} else {
				$later2++;
			}
		}
		$row = 2;

		foreach ($UtilityData as $utl) {
			foreach ($utl as $key => $val) {
				if (!empty($val) && is_numeric($val)) {
					$val = round($val, 4);
					if ($val == 0) {
						$val = '';
					}
				}
				if (isset($val) && ($key == 'local_unit')) {
					$val = html_entity_decode($areaUnit[$val]);
				}
				if ($val === '0') {
					$val = '';
				}
				if (array_key_exists($key, $cells)) {
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
				}
			}
			$row++;
		}

		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Group Utility', $data_action);
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Group Utility Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}

	public function export_prev_utility()
	{
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		ini_set('memory_limit', '-1');

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");
		//Start adding next sheets
		$columns = $sites = $siteUtilityData = [];
		// Add new sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Export Utility');
		$objPHPExcel->getActiveSheet()->setTitle('Export Utility');
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray(
			array(
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FFF2CC')
				)
			)
		);
		// $objPHPExcel->getActiveSheet()->setAutoFilter('A1:B1');
		// $objPHPExcel->getActiveSheet()->setAutoFilter(
		//     $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
		// );
		$areaUnit = [
			'm&#178;',
			'ft&#178;'
		];
		$columns["site_location_name"] = 'Site Name';
		$columns["attribute"] = 'Attribute';
		$columns["month_id"] = 'Reporting Month';
		$columns["year_id"] = 'Reporting Year';
		$columns["local_unit"] = 'Local Unit';
		$columns["local_currency"] = 'Local Currency';
		$columns['total_purchased_electricity'] = 'Electricity: Purchased Electricity';
		$columns['tariff'] = 'Electricity: Purchased Electricity Tariff ($)';
		$columns["maximum_demand"] = 'Maximum Demand KVA/KW';
		$columns["maximum_demand_price"] = 'Maximum Demand Tariff ($/KVA || $/KW)';
		$columns["fixed_fees"] = 'Fixed Fees Cost ($)';
		$columns["electricity_total_budget"] = 'Electricity: Total Budgeted';
		$columns["electricity_total_budget_cost"] = 'Electricity: Total Budgeted Cost ($)';
		$columns["total_renewable_energy_production"] = 'Total Renewable Energy Production';
		$columns["total_renewable_energy_production_cost"] = 'Total Renewable Energy Cost ($)';
		$columns["total_renewable_energy_exported"] = 'Total Renewable Energy Exported';
		$columns["total_renewable_energy_exported_cost"] = 'Total Renewable Energy Exported Cost ($)';
		$columns["onsite_generators_quantity"] = 'Onsite Generators';
		$columns["total_onsite_generators_cost"] = 'Onsite Generators $';
		$columns["onsite_generators_fuel_oil_quantity"] = 'Onsite Generators Fuel Oil';
		$columns["onsite_generators_fuel_oil_price"] = 'Onsite Generator Fuel Oil $';
		$columns["onsite_generators_natural_gas_quantity"] = 'Onsite Generators Natural Gas';
		$columns["onsite_generators_natural_gas_price"] = 'Onsite Generators Natural Gas $';
		$columns["total_electricity_cost"] = 'Electricity: Total Electricity Cost';
		$columns["total_electricity_kwh"] = 'Electricity: Total Electricity';
		$columns["electricity_unit"] = 'Electricity: Utility unit supply of Electricity';
		$columns["lpg_kitchen"] = 'LPG: Kitchen ';
		$columns["lpg_kitchen_rate"] = 'LPG: Kitchen $';
		$columns["lpg_fixed_cost"] = 'LPG: Fixed Cost';
		$columns["lpg_total_budget"] = 'LPG: Total Budgeted';
		$columns["lpg_total_budget_cost"] = 'LPG: Total Budgeted Cost ($)';
		$columns["lpg_hot_water_boilers"] = 'LPG: Hot-Water Boilers';
		$columns["lpg_hot_water_boilers_rate"] = 'LPG: Hot-Water Boilers $';
		$columns["lpg_steam_boilers"] = 'LPG: Steam Boilers';
		$columns["lpg_steam_boilers_rate"] = 'LPG: Steam Boilers $';
		$columns["lpg_unit"] = 'LPG: Utility unit supply of LPG';
		$columns["district_heating"] = 'District Energy: Heating';
		$columns["district_heating_rate"] = 'District Energy: Heating $';
		$columns["district_heating_fixed_cost"] = 'District Energy: Heating Fixed Cost';
		$columns["district_heating_total_budget"] = 'District Energy: Heating Total Budgeted';
		$columns["district_heating_total_budget_cost"] = 'District Energy: Heating Total Budgeted Cost ($)';
		$columns["district_heating_unit"] = 'District Energy: Utility unit supply of District Heating';
		$columns["district_cooling"] = 'District Energy: Cooling';
		$columns["district_cooling_rate"] = 'District Energy: Cooling $';
		$columns["district_cooling_total_budget"] = 'District Energy: Cooling Total Budgeted';
		$columns["district_cooling_total_budget_cost"] = 'District Energy: Cooling Total Budgeted Cost ($)';
		$columns["district_cooling_fixed_cost"] = 'District Energy: Cooling Fixed Cost';
		$columns["district_cooling_unit"] = 'District Energy: Utility unit supply of District Cooling';
		$columns["water_utility_supply"] = 'Water: Utility Supply';
		$columns["water_utility_supply_rate"] = 'Water: Utility Supply $';
		$columns["water_fixed_cost"] = 'Water: Fixed Cost';
		$columns["water_total_consumption_budget"] = 'Water: Total Budgeted';
		$columns["water_total_consumption_budget_cost"] = 'Water: Total Budgeted Cost ($)';
		$columns["waste_water"] = 'Water Waste';
		$columns["waste_water_rate"] = 'Water Waste';
		$columns["water_ro"] = 'Water: RO';
		$columns["water_ro_rate"] = 'Water: RO';
		$columns["water_Cisterns"] = 'Water: Cisterns';
		$columns["water_Cisterns_rate"] = 'Water: Cisterns';
		$columns["water_unit"] = 'Water: Utility unit supply of Water';
		$columns["fuel_oil_hot_water_boilers"] = 'Fuel Oil: Hot-Water Boilers';
		$columns["fuel_oil_hot_water_boilers_rate"] = 'Fuel Oil: Hot-Water Boilers';
		$columns["fuel_oil_steam_boilers"] = 'Fuel Oil: Steam Boilers';
		$columns["fuel_oil_steam_boilers_rate"] = 'Fuel Oil: Steam Boilers';
		$columns["fuel_oil_others"] = 'Fuel Oil: Others';
		$columns["fuel_oil_others_rate"] = 'Fuel Oil: Others';
		$columns["fuel_total_budget"] = 'Fuel Oil: Total Budgeted';
		$columns["fuel_total_budget_cost"] = 'Fuel Oil: Total Budgeted Cost ($)';
		$columns["fuel_oil_unit"] = 'Fuel Oil: Utility unit supply of Fuel Oil';
		$columns["natural_gas_hot_water_boilers"] = 'Natural Gas: Hot-Water Boilers';
		$columns["natural_gas_hot_water_boilers_rate"] = 'Natural Gas: Hot-Water Boilers';
		$columns["natural_gas_steam_boilers"] = 'Natural Gas: Steam Boilers';
		$columns["natural_gas_steam_boilers_rate"] = 'Natural Gas: Steam Boilers';
		$columns["natural_gas_kitchen"] = 'Natural Gas: Kitchen';
		$columns["natural_gas_kitchen_rate"] = 'Natural Gas: Kitchen';
		$columns["natural_gas_fixed_cost"] = 'Natural Gas: Fixed Cost';
		$columns["natural_gas_total_budget"] = 'Natural Gas: Total Budgeted';
		$columns["natural_gas_total_budget_cost"] = 'Natural Gas: Total Budgeted Cost ($)';
		$columns["natural_gas_unit"] = 'Natural Gas: Utility unit supply of Natural Gas';
		$columns["total_room_night"] = 'Room Nights';
		$columns["total_room_night_budget"] = 'Room Nights Budget';
		$columns["total_guests_budget"] = 'Total Guests Budget';
		$columns["total_guests"] = 'Total Guests';
		$columns["total_laundered"] = 'Laundry Load';
		$columns["cdd"] = "Cooling Degree Day";
		$columns["hdd"] = "Heating Degree Day";
		$columns['onsite_generators_quantity'] = 'Onsite Generators';
		$columns["average_pf"] = 'Average PF';
		$columns["total_fb_services"]           = 'Food Covers';
		$columns["revenue"]                     = 'Revenue';
		$columns['total_f_b_sales'] = "Total F and B Sales";
		$columns['forex'] = "Forex";


		$columns['employee_living_quarter_electricity'] = 'Employee Quarter Electricity';
		$columns['employee_living_quarter_fuel_oil'] = 'Employee Quarter Fuel Oil/Diesel';
		$columns['employee_living_quarter_lpg'] = 'Employee Quarter LPG';
		$columns['employee_living_quarter_natural_gas'] = 'Employee Quarter Natural Gas';
		$columns['employee_living_quarter_district_heating'] = 'Employee Quarter District Heating';
		$columns['employee_living_quarter_district_cooling'] = 'Employee Quarter District Cooling';
		$columns['employee_living_quarter_water'] = 'Employee Quarter Water';

		$columns['employee_living_quarter_offsite_electricity'] = 'Employee Quarter Electricity';
		$columns['employee_living_quarter_offsite_fuel_oil'] = 'Employee Quarter Fuel Oil/Diesel';
		$columns['employee_living_quarter_offsite_lpg'] = 'Employee Quarter LPG';
		$columns['employee_living_quarter_offsite_natural_gas'] = 'Employee Quarter Natural Gas';
		$columns['employee_living_quarter_offsite_district_heating'] = 'Employee Quarter District Heating';
		$columns['employee_living_quarter_offsite_district_cooling'] = 'Employee Quarter District Cooling';
		$columns['employee_living_quarter_offsite_water'] = 'Employee Quarter Water';

		// Charged by meter values store rental
		$columns['rental_program_residence_electricity'] = 'Rental Electricity';
		$columns['rental_program_residence_electricity_rate'] = 'Rental Electricity Rate';
		$columns['rental_program_residence_electricity_cost'] = 'Rental Electricity Cost';
		$columns['rental_program_residence_fuel_oil'] = 'Rental Fuel/Diesel Oil';
		$columns['rental_program_residence_fuel_oil_rate'] = 'Rental Fuel/Diesel Oil Rate';
		$columns['rental_program_residence_fuel_oil_cost'] = 'Rental Fuel/Diesel Oil Cost';
		$columns['rental_program_residence_lpg'] = 'Rental Lpg';
		$columns['rental_program_residence_lpg_rate'] = 'Rental Lpg Rate';
		$columns['rental_program_residence_lpg_cost'] = 'Rental Lpg Cost';
		$columns['rental_program_residence_natural_gas'] = 'Rental Natural Gas';
		$columns['rental_program_residence_natural_gas_rate'] = 'Rental Natural Gas Rate';
		$columns['rental_program_residence_natural_gas_cost'] = 'Rental Natural Gas Cost';
		$columns['rental_program_residence_district_cooling'] = 'Rental District Cooling';
		$columns['rental_program_residence_district_cooling_rate'] = 'Rental District Cooling Rate';
		$columns['rental_program_residence_district_cooling_cost'] = 'Rental District Cooling Cost';
		$columns['rental_program_residence_district_heating'] = 'Rental District Heating';
		$columns['rental_program_residence_district_heating_rate'] = 'Rental District Heating Rate';
		$columns['rental_program_residence_district_heating_cost'] = 'Rental District Heating Cost';
		$columns['rental_program_residence_water'] = 'Rental Water';
		$columns['rental_program_residence_water_rate'] = 'Rental Water Rate';
		$columns['rental_program_residence_water_cost'] = 'Rental Water Cost';

		// Charged by meter values store private
		$columns['private_program_electricity'] = 'Private Electricity';
		$columns['private_program_electricity_rate'] = 'Private Electricity Rate';
		$columns['private_program_electricity_cost'] = 'Private Electricity Cost';
		$columns['private_program_fuel_oil'] = 'Private Fuel/Diesel Oil';
		$columns['private_program_fuel_oil_rate'] = 'Private Fuel/Diesel Oil Rate';
		$columns['private_program_fuel_oil_cost'] = 'Private Fuel/Diesel Oil Cost';
		$columns['private_program_lpg'] = 'Private Lpg';
		$columns['private_program_lpg_rate'] = 'Private Lpg Rate';
		$columns['private_program_lpg_cost'] = 'Private Lpg Cost';
		$columns['private_program_natural_gas'] = 'Private Natural Gas';
		$columns['private_program_natural_gas_rate'] = 'Private Natural Gas Rate';
		$columns['private_program_natural_gas_cost'] = 'Private Natural Gas Cost';
		$columns['private_program_district_cooling'] = 'Private District Cooling';
		$columns['private_program_district_cooling_rate'] = 'Private District Cooling Rate';
		$columns['private_program_district_cooling_cost'] = 'Private District Cooling Cost';
		$columns['private_program_district_heating'] = 'Private District Heating';
		$columns['private_program_district_heating_rate'] = 'Private District Heating Rate';
		$columns['private_program_district_heating_cost'] = 'Private District Heating Cost';
		$columns['private_program_water'] = 'Private Water';
		$columns['private_program_water_rate'] = 'Private Water Rate';
		$columns['private_program_water_cost'] = 'Private Water Cost';
		$decimal_places = 2;
		$this->load->model('sites/sites_model');
		$UtilityData = $this->sites_model->getAllUtility();

		//remove duplicate utility record
		foreach ($UtilityData as $key => $utl) {
			if (!isset($siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']]) && empty($siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']])) {
				$siteUtilityData[$utl['site_id'] . '_' . $utl['year_id'] . '_' . $utl['month_id']] = $utl;
				$UtilityData[$key] = $utl;
			} else {
				unset($UtilityData[$key]);
			}
		}

		//adding purchased electricity records to utility record
		foreach ($UtilityData as $key => $utl) {
			$current_utility = $utl;
			$this->load->model('utilities/utilities_model');
			$this->utilities_model->utilities_month = $utl['month_id'];
			$this->utilities_model->utilities_year = $utl['year_id'];
			$this->utilities_model->site_id = $utl['site_id'];
			$electricityTariff = $this->utilities_model->getElectricityTariff();

			$temp = 0;
			foreach ($electricityTariff as $single) {
				if ($temp == 0) {
					$current_utility['tariff'] = round($single['tariff'], $decimal_places);
					$current_utility['total_kwh'] = round($single['total_kwh'], $decimal_places);
				} else {
					$current_utility['tariff' . $temp] = round($single['tariff'], $decimal_places);
					$current_utility['total_kwh' . $temp] = round($single['total_kwh'], $decimal_places);
				}
				$temp++;
			}
			$UtilityData[$key] = $current_utility;
		}

		$cells = array();
		$later1 = "";
		$later2 = 'A';
		$flag = 0;
		foreach ($columns as $key => $column) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
			$cells[$key] = $later1 . $later2;
			$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
			if ($later2 == 'Z') {
				if ($flag == 0) {
					$later1 = 'A';
					$flag = 1;
				} else {
					$later1++;
				}
				$later2 = 'A';
			} else {
				$later2++;
			}
		}
		$row = 2;

		foreach ($UtilityData as $utl) {
			foreach ($utl as $key => $val) {
				if (!empty($val) && is_numeric($val)) {
					$val = round($val, 4);
					if ($val == 0) {
						$val = '';
					}
				}
				if (isset($val) && ($key == 'local_unit')) {
					$val = html_entity_decode($areaUnit[$val]);
				}
				if ($val === '0') {
					$val = '';
				}
				if (array_key_exists($key, $cells)) {
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
				}
			}
			$row++;
		}

		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Group Utility All', $data_action);
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Group Utility Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
	}

	public function export_site_info()
	{
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		ini_set('memory_limit', '-1');
		ini_set('MAX_EXECUTION_TIME', '-1');
		$this->load->model('countries/countries_model');

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");
		//Start adding next sheets
		$columns = $sites = [];
		$site_types = $this->config->config['sites_type'];
		$regions = $this->sites_model->region_list();
		$countries = $this->countries_model->getCountries();
		$laundry_types = array('1' => 'Outsourced', '0' => 'On Site');
		$electricity_units = GetUtilityDropdownFromConstant('electricity');
		$fuel_oil_units = GetUtilityDropdownFromConstant('fuel_oil');
		$lpg_units = GetUtilityDropdownFromConstant('lpg');
		$water_units = GetUtilityDropdownFromConstant('water');
		$natural_gas_units = GetUtilityDropdownFromConstant('natural_gas');
		$district_cooling_units = GetUtilityDropdownFromConstant('district_cooling');
		$district_heating_units = GetUtilityDropdownFromConstant('district_heating');
		$areaUnit = [
			'm&#178;',
			'ft&#178;'
		];
		// Add new sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Export Site Info');
		$objPHPExcel->getActiveSheet()->setTitle('Export Site Info');
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$objPHPExcel->getDefaultStyle()->applyFromArray($style);

		$columns['site_location_name'] = 'Site Location Name';
		$columns['attribute'] = 'Attribute';
		$columns['rental_program_attribute'] = 'Rental Program Attribute';
		$columns['employee_quarter_attribute'] = 'Employee Quarter Attribute';
		$columns['residences_attribute'] = 'Residences Attribute';
		$columns['site_type'] = 'Site Type';
		$columns['region_id'] = 'Region Id';
		$columns['country_id'] = 'Country Id';
		$columns['site_year_built'] = 'Site Year Built';
		$columns['site_color'] = 'Site Color';
		$columns['baseline_regression_year'] = 'Baseline Regression Year';
		$columns['local_currency'] = 'Local Currency';
		$columns['local_unit'] = 'Local Area Unit';
		$columns['station_id'] = 'Weather';
		$columns['city'] = 'City';
		$columns['site_location_latitude'] = 'Site Location Latitude';
		$columns['site_location_longitude'] = 'Site Location Longitude';
		// $columns['base_cdd_temprature'] = 'Base Cdd Temprature';
		// $columns['base_hdd_temprature'] = 'Base Hdd Temprature';
		$columns['comments'] = 'Special Comments';
		$columns['residence_types'] = 'Residence Types';
		$columns['site_builtup_area'] = 'Site Builtup Area';
		$columns['cooled_builtup_area'] = 'Cooled Builtup Area';
		$columns['rooms_keys'] = 'Rooms Keys';
		$columns['indoor_parking_area'] = 'Indoor Parking Area';
		$columns['total_meeting_area'] = 'Total Meeting Area';
		$columns['total_spa_area'] = 'Total Spa Area';
		$columns['total_guest_room_area'] = 'Total Guest Room Area';
		$columns['hotel_rooms_area'] = 'Hotel Rooms Area';
		$columns['residential_common_area'] = 'Residential Common Area';
		$columns['employee_living_quarters_area'] = 'Employee Living Quarters Area';
		$columns['restaurant_area'] = 'Restaurant Area';
		$columns['landscaped_area'] = 'Landscaped Area';
		$columns['total_split_dx_unit'] = 'Total Split Dx Unit';
		$columns['total_rate_split_dx_unit'] = 'Total Rate Split Dx Unit';
		$columns['total_vrv_unit'] = 'Total Vrv Unit';
		$columns['total_vrv'] = 'Total Vrv';
		$columns['outdoor_pools'] = 'Outdoor Pools';
		$columns['indoor_pools'] = 'Indoor Pools';
		$columns['month_year_operation'] = 'Month Year Operation';
		$columns['chilled_water_system_type'] = 'Chilled Water System Type';
		$columns['chilled_water_system_total_rate'] = 'Chilled Water System Total Rate';
		$columns['chilled_water_system_type2'] = 'Chilled Water System Type2';
		$columns['chilled_water_system_total_rate2'] = 'Chilled Water System Total Rate2';
		$columns['elcetrical_hw_total'] = 'Elcetrical Hw Total';
		$columns['elcetrical_hw_total_capacity'] = 'Elcetrical Hw Total Capacity';
		$columns['elcetrical_hw_total_power'] = 'Elcetrical Hw Total Power';
		$columns['vehicle_electric'] = 'Vehicle Electric';
		$columns['vehicle_petrol'] = 'Vehicle Petrol';
		$columns['f_b_service'] = 'F B Service';
		$columns['f_b_services_operated'] = 'F B Services Operated';
		$columns['f_b_services_outsourced'] = 'F B Services Outsourced';
		$columns['rental_program_residence'] = 'Rental Program Residence';
		$columns['rental_private_residence'] = 'Rental Private Residence';
		$columns['rental_program_residence_conditioned'] = 'Rental Program Residence Conditioned';
		$columns['rental_private_residence_conditioned'] = 'Rental Private Residence Conditioned';
		$columns['rental_program_residence_suites'] = 'Rental Program Residence Suites';
		$columns['rental_private_residence_suites'] = 'Rental Private Residence Suites';
		$columns['room_area_rental_program'] = 'Room Area Rental Program';
		$columns['room_area_private_residence'] = 'Room Area Private Residence';
		$columns['ro_plant_capacity'] = 'Ro Plant Capacity';
		$columns['calorifiers_unit'] = 'Calorifiers Unit';
		$columns['calorifiers_volume'] = 'Calorifiers Volume';
		// $columns['threshold'] = 'Threshold';
		$columns['electricity_emission_factor'] = 'Electricity Emission Factor';
		$columns['electricity_emission_factor_percentage'] = 'Electricity Emission Factor Percentage';
		$columns['fuel_emission_factor'] = 'Fuel Emission Factor';
		$columns['lpg_emission_factor'] = 'Lpg Emission Factor';
		$columns['natural_gas_emission_factor'] = 'Natural Gas Emission Factor';
		$columns['district_cooling_emission_factor'] = 'District Cooling Emission Factor';
		$columns['district_heating_emission_factor'] = 'District Heating Emission Factor';
		$columns['energy_intensity_annual_target'] = 'Energy Intensity Annual Target';
		$columns['ghg_intensity_annual_target'] = 'Ghg Intensity Annual Target';
		$columns['water_intensity_annual_target'] = 'Water Intensity Annual Target';
		$columns['waste_intensity_annual_target'] = 'Waste Intensity Annual Target';
		$columns['energy_intensity_benchmark_target'] = 'Energy Intensity Benchmark Target';
		$columns['ghg_intensity_benchmark_target'] = 'Ghg Intensity Benchmark Target';
		$columns['water_intensity_benchmark_target'] = 'Water Intensity Benchmark Target';
		$columns['waste_intensity_benchmark_target'] = 'Waste Intensity Benchmark Target';
		$columns['laundry_type'] = 'Laundry Type';
		$columns['laundry_fuel_type'] = 'Laundry Fuel Type';
		$columns['chsb_reporting'] = 'Chsb Reporting';
		$columns['chsb_segment'] = 'Chsb Segment';
		// $columns['csr'] = 'Csr';
		// $columns['daily_metering'] = 'Daily Metering';
		$columns['is_chilled_water_system'] = 'Is Chilled Water System';
		$columns['is_split_dx_unit'] = 'Is Split Dx Unit';
		$columns['is_vrv'] = 'Is Vrv';
		$columns['is_ro_plant'] = 'Is Ro Plant';
		$columns['is_renewable_energy'] = 'Is Renewable Energy';
		// $columns['is_used_in_cron'] = 'Is Used In Cron';
		// $columns['is_hourly'] = 'Is Hourly';
		$columns['show_utility_electricity'] = 'Show Utility Electricity';
		$columns['show_utility_fuel_oil'] = 'Show Utility Fuel Oil';
		$columns['show_utility_lpg'] = 'Show Utility Lpg';
		$columns['show_utility_water'] = 'Show Utility Water';
		$columns['show_utility_irrigation_water'] = 'Show Utility Irrigation Water';
		$columns['show_utility_natural_gas'] = 'Show Utility Natural Gas';
		$columns['show_utility_district_cooling'] = 'Show Utility District Cooling';
		$columns['show_utility_district_heating'] = 'Show Utility District Heating';
		$columns['show_utility_district_heating_boiler'] = 'Show Utility District Heating Boiler';
		$columns['show_waste_management'] = 'Show Waste Management';
		$columns['show_utility_fleet'] = 'Show Utility Fleet';
		$columns['show_utility_water_waste'] = 'Show Utility Water Waste';
		$columns['show_total_utility_notification'] = 'Show Total Utility Notification';
		$columns['utility_unit_electricity'] = 'Utility Unit Electricity';
		$columns['utility_unit_fuel_oil'] = 'Utility Unit Fuel Oil';
		$columns['utility_unit_lpg'] = 'Utility Unit Lpg';
		$columns['utility_unit_water'] = 'Utility Unit Water';
		$columns['utility_unit_natural_gas'] = 'Utility Unit Natural Gas';
		$columns['utility_unit_district_cooling'] = 'Utility Unit District Cooling';
		$columns['utility_unit_district_heating'] = 'Utility Unit District Heating';
		// Residence Data
		$columns['electricity_private_program_consumption'] = 'Electricity Private Program Consumption';
		$columns['electricity_private_program_fixed'] = 'Electricity Private Program Fixed';
		$columns['electricity_private_program_float'] = 'Electricity Private Program Float';
		$columns['electricity_private_program_hotel_connected'] = 'Electricity Private Program Hotel Connected';
		$columns['electricity_rental_program_residence_consumption'] = 'Electricity Rental Program Residence Consumption';
		$columns['electricity_rental_program_residence_fixed'] = 'Electricity Rental Program Residence Fixed';
		$columns['electricity_rental_program_residence_float'] = 'Electricity Rental Program Residence Float';
		$columns['electricity_rental_program_residence_hotel_connected'] = 'Electricity Rental Program Residence Hotel Connected';
		$columns['lpg_private_program_consumption'] = 'Lpg Private Program Consumption';
		$columns['lpg_private_program_fixed'] = 'Lpg Private Program Fixed';
		$columns['lpg_private_program_float'] = 'Lpg Private Program Float';
		$columns['lpg_private_program_hotel_connected'] = 'Lpg Private Program Hotel Connected';
		$columns['lpg_rental_program_residence_consumption'] = 'Lpg Rental Program Residence Consumption';
		$columns['lpg_rental_program_residence_fixed'] = 'Lpg Rental Program Residence Fixed';
		$columns['lpg_rental_program_residence_float'] = 'Lpg Rental Program Residence Float';
		$columns['lpg_rental_program_residence_hotel_connected'] = 'Lpg Rental Program Residence Hotel Connected';
		$columns['water_private_program_consumption'] = 'Water Private Program Consumption';
		$columns['water_private_program_fixed'] = 'Water Private Program Fixed';
		$columns['water_private_program_float'] = 'Water Private Program Float';
		$columns['water_private_program_hotel_connected'] = 'Water Private Program Hotel Connected';
		$columns['water_rental_program_residence_consumption'] = 'Water Rental Program Residence Consumption';
		$columns['water_rental_program_residence_fixed'] = 'Water Rental Program Residence Fixed';
		$columns['water_rental_program_residence_float'] = 'Water Rental Program Residence Float';
		$columns['water_rental_program_residence_hotel_connected'] = 'Water Rental Program Residence Hotel Connected';
		$columns['district_heating_private_program_consumption'] = 'District Heating Private Program Consumption';
		$columns['district_heating_private_program_fixed'] = 'District Heating Private Program Fixed';
		$columns['district_heating_private_program_float'] = 'District Heating Private Program Float';
		$columns['district_heating_private_program_hotel_connected'] = 'District Heating Private Program Hotel Connected';
		$columns['district_heating_rental_program_residence_consumption'] = 'District Heating Rental Program Residence Consumption';
		$columns['district_heating_rental_program_residence_fixed'] = 'District Heating Rental Program Residence Fixed';
		$columns['district_heating_rental_program_residence_float'] = 'District Heating Rental Program Residence Float';
		$columns['district_heating_rental_program_residence_hotel_connected'] = 'District Heating Rental Program Residence Hotel Connected';
		$columns['natural_gas_private_program_consumption'] = 'Natural Gas Private Program Consumption';
		$columns['natural_gas_private_program_fixed'] = 'Natural Gas Private Program Fixed';
		$columns['natural_gas_private_program_float'] = 'Natural Gas Private Program Float';
		$columns['natural_gas_private_program_hotel_connected'] = 'Natural Gas Private Program Hotel Connected';
		$columns['natural_gas_rental_program_residence_consumption'] = 'Natural Gas Rental Program Residence Consumption';
		$columns['natural_gas_rental_program_residence_fixed'] = 'Natural Gas Rental Program Residence Fixed';
		$columns['natural_gas_rental_program_residence_float'] = 'Natural Gas Rental Program Residence Float';
		$columns['natural_gas_rental_program_residence_hotel_connected'] = 'Natural Gas Rental Program Residence Hotel Connected';
		$columns['district_cooling_private_program_consumption'] = 'District Cooling Private Program Consumption';
		$columns['district_cooling_private_program_fixed'] = 'District Cooling Private Program Fixed';
		$columns['district_cooling_private_program_float'] = 'District Cooling Private Program Float';
		$columns['district_cooling_private_program_hotel_connected'] = 'District Cooling Private Program Hotel Connected';
		$columns['district_cooling_rental_program_residence_consumption'] = 'District Cooling Rental Program Residence Consumption';
		$columns['district_cooling_rental_program_residence_fixed'] = 'District Cooling Rental Program Residence Fixed';
		$columns['district_cooling_rental_program_residence_float'] = 'District Cooling Rental Program Residence Float';
		$columns['district_cooling_rental_program_residence_hotel_connected'] = 'District Cooling Rental Program Residence Hotel Connected';
		$columns['fuel_oil_private_program_consumption'] = 'Fuel Oil Private Program Consumption';
		$columns['fuel_oil_private_program_fixed'] = 'Fuel Oil Private Program Fixed';
		$columns['fuel_oil_private_program_float'] = 'Fuel Oil Private Program Float';
		$columns['fuel_oil_private_program_hotel_connected'] = 'Fuel Oil Private Program Hotel Connected';
		$columns['fuel_oil_rental_program_residence_consumption'] = 'Fuel Oil Rental Program Residence Consumption';
		$columns['fuel_oil_rental_program_residence_fixed'] = 'Fuel Oil Rental Program Residence Fixed';
		$columns['fuel_oil_rental_program_residence_float'] = 'Fuel Oil Rental Program Residence Float';
		$columns['fuel_oil_rental_program_residence_hotel_connected'] = 'Fuel Oil Rental Program Residence Hotel Connected';

		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$sites = $this->sites_model->get_site_list_helper();

		$cells = array();
		$later1 = "";
		$later2 = 'A';
		$flag = 0;
		foreach ($columns as $key => $column) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
			$cells[$key] = $later1 . $later2;
			$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
			if ($later2 == 'Z') {
				if ($flag == 0) {
					$later1 = 'A';
					$flag = 1;
				} else {
					$later1++;
				}
				$later2 = 'A';
			} else {
				$later2++;
			}
		}

		$row = 2;
		if (isset($sites) && !empty($sites)) {
			foreach ($sites as $keySite => $site) {
				$siteDetail = $this->sites_model->get_site_detail($keySite, $user_id, $role_id);
				if (isset($siteDetail) && !empty($siteDetail)) {
					foreach ($siteDetail as $key => $val) {
						if ($key == 'local_unit') {
							$val = html_entity_decode($areaUnit[$val]);
						}
						if ($key == 'laundry_type') {
							$val = $laundry_types[$val];
						}
						if (!empty($val) && is_numeric($val)) {
							$val = round($val, 4);
							if ($val == 0) {
								$val = '';
							}
						}
						if (!is_string($val) && !is_numeric($val)) {
							$val = '';
						}
						if ($val === '0') {
							$val = '';
						}
						if (isset($val) && !empty($val)) {
							if ($key == 'region_id') {
								$val = $regions[$val];
							} else if ($key == 'country_id') {
								$SearchKey = array_search($val, array_column(array_column($countries, 'c'), 'id'));
								$val = $countries[$SearchKey]['c']['country'];
							} else if ($key == 'site_type') {
								$val = $site_types[$val];
							} else if (in_array($key, ['chsb_reporting', 'chsb_segment', 'csr', 'daily_metering', 'is_chilled_water_system', 'is_split_dx_unit', 'is_vrv', 'is_ro_plant', 'is_renewable_energy', 'is_used_in_cron', 'is_hourly', 'show_utility_electricity', 'show_utility_fuel_oil', 'show_utility_lpg', 'show_utility_water', 'show_utility_irrigation_water', 'show_utility_natural_gas', 'show_utility_district_cooling', 'show_utility_district_heating', 'show_utility_district_heating_boiler', 'show_waste_management', 'show_utility_fleet', 'show_utility_water_waste', 'show_total_utility_notification'])) {
								if ($val == 1) {
									$val = 'Yes';
								}
							} else if ($key == 'utility_unit_electricity') {
								$val = $electricity_units[$val];
							} else if ($key == 'utility_unit_fuel_oil') {
								$val = $fuel_oil_units[$val];
							} else if ($key == 'utility_unit_lpg') {
								$val = $lpg_units[$val];
							} else if ($key == 'utility_unit_water') {
								$val = $water_units[$val];
							} else if ($key == 'utility_unit_natural_gas') {
								$val = $natural_gas_units[$val];
							} else if ($key == 'utility_unit_district_cooling') {
								$val = $district_cooling_units[$val];
							} else if ($key == 'utility_unit_district_heating') {
								$val = $district_heating_units[$val];
							} else if ($key == 'residence_types') {
								$residenceType1 = lang('rental-program');
								$residenceType2 = lang('rental-private');
								$residenceType3 = lang('employee-living-quarters-area');
								$residenceType4 = lang('employee-living-quarters-area-offsite');
								$array = explode(",", $val);
								$residence = [];
								foreach ($array as $keytemp => $valueTemp) {
									if ($valueTemp == 1) {
										array_push($residence, $residenceType1);
									}
									if ($valueTemp == 2) {
										array_push($residence, $residenceType2);
									}
									if ($valueTemp == 3) {
										array_push($residence, $residenceType3);
									}
									if ($valueTemp == 4) {
										array_push($residence, $residenceType4);
									}
								}
								if (sizeof($residence) > 1) {
									$val = implode(",", $residence);
								} else {
									$val = $residence[0];
								}
							}
						}

						if (array_key_exists($key, $cells)) {
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
						}
					}
				}
				$row++;
			}
		}
		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Group Site Info', $data_action);
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Site Info Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}


	public function export_utility_choices()
	{
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		ini_set('memory_limit', '-1');
		ini_set('MAX_EXECUTION_TIME', '-1');

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");
		//Start adding next sheets
		$columns = $sites = [];
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		// Add new sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Export Utility Choices');
		$objPHPExcel->getActiveSheet()->setTitle('Export Site Utility Choices');
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$objPHPExcel->getActiveSheet()->setAutoFilter('A1:S1');
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$objPHPExcel->getDefaultStyle()->applyFromArray($style);

		$columns['site_location_name'] = 'Site Location Name';
		$columns['attribute'] = 'Attribute';
		$columns['show_utility_electricity'] = 'Show Electricity';
		$columns['utility_unit_electricity'] = 'Electricity Unit Choice';
		$columns['show_utility_fuel_oil'] = 'Show Fuel Oil/Diesel';
		$columns['utility_unit_fuel_oil'] = 'Fuel Oil/Diesel Unit Choice';
		$columns['show_utility_lpg'] = 'Show Lpg';
		$columns['utility_unit_lpg'] = 'Lpg Unit Choice';
		$columns['show_utility_water'] = 'Show water';
		$columns['utility_unit_water'] = 'Water Unit Choice';
		$columns['show_utility_natural_gas'] = 'Show Natural Gas';
		$columns['utility_unit_natural_gas'] = 'Natural Gas Unit Choice';
		$columns['show_utility_district_cooling'] = 'Show District Cooling';
		$columns['utility_unit_district_cooling'] = 'District Cooling Unit Choice';
		$columns['show_utility_district_heating'] = 'Show District Heating';
		$columns['utility_unit_district_heating'] = 'District Heating Unit Choice';
		$columns['show_utility_waste_water'] = 'Show waste water';
		$columns['show_utility_waste_management'] = 'Show Waste Management';
		$columns['show_utility_fleet'] = 'Show Utility Fleet';
		$columns['show_utility_irrigation_water'] = 'Show Irrigation Water';
		$site_id = 1;
		$sites = $this->sites_model->get_site_listing_for_users($site_id, $role_id, $user_id);
		$cells = array();
		$later1 = "";
		$later2 = 'A';
		$flag = 0;
		foreach ($columns as $key => $column) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
			$cells[$key] = $later1 . $later2;
			$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
			if ($later2 == 'Z') {
				if ($flag == 0) {
					$later1 = 'A';
					$flag = 1;
				} else {
					$later1++;
				}
				$later2 = 'A';
			} else {
				$later2++;
			}
		}

		$row = 2;
		if (isset($sites) && !empty($sites)) {
			foreach ($sites as $keyS => $site) {
				$keySite = $site['s']['id'];
				$siteDetail = $this->sites_model->get_site_detail($keySite, $user_id, $role_id);
				if (isset($siteDetail) && !empty($siteDetail)) {
					foreach ($siteDetail as $key => $val) {
						$electricity_unit = GetSiteUtilityUnitName($keySite, 'electricity');
						$fuel_oil_unit = GetSiteUtilityUnitName($keySite, 'fuel_oil');
						$lpg_unit = GetSiteUtilityUnitName($keySite, 'lpg');
						$water_unit = GetSiteUtilityUnitName($keySite, 'water');
						$natural_gas_unit = GetSiteUtilityUnitName($keySite, 'natural_gas');
						$district_cooling_unit = GetSiteUtilityUnitName($keySite, 'district_cooling');
						$district_heating_unit = GetSiteUtilityUnitName($keySite, 'district_heating');

						if ($key == 'utility_unit_electricity') {
							$val = isset($siteDetail['show_utility_electricity']) && $siteDetail['show_utility_electricity'] == 1 ? $electricity_unit : '-';
						} else if ($key == 'utility_unit_fuel_oil') {
							$val = isset($siteDetail['show_utility_fuel_oil']) && $siteDetail['show_utility_fuel_oil'] == 1 ? $fuel_oil_unit : '-';
						} else if ($key == 'utility_unit_lpg') {
							$val = isset($siteDetail['show_utility_lpg']) && $siteDetail['show_utility_lpg'] == 1 ? $lpg_unit : '-';
						} else if ($key == 'utility_unit_water') {
							$val = isset($siteDetail['show_utility_water']) && $siteDetail['show_utility_water'] == 1 ? $water_unit : '-';
						} else if ($key == 'utility_unit_natural_gas') {
							$val = isset($siteDetail['show_utility_natural_gas']) && $siteDetail['show_utility_natural_gas'] == 1 ? $natural_gas_unit : '-';
						} else if ($key == 'utility_unit_district_heating') {
							$val = isset($siteDetail['show_utility_district_heating']) && $siteDetail['show_utility_district_heating'] == 1 ? $district_heating_unit : '-';
						} else if ($key == 'utility_unit_district_cooling') {
							$val = isset($siteDetail['show_utility_district_cooling']) && $siteDetail['show_utility_district_cooling'] == 1 ? $district_cooling_unit : '-';
						} else if (strpos($key, 'show_utility_') != false) {
							$val = (isset($val) && $val == 1) ? 'Yes' : ' No';
						}

						if (array_key_exists($key, $cells)) {
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
						}
					}
				}
				$row++;
			}
		}

		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Utility Choices', $data_action);
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Site Utility Choices Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}

	public function export_utility_invoices()
	{
		ini_set('memory_limit', '-1');
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$filesToZip = [];
		$year = $_POST['YearFormat'];
		$selectedSite = $_POST['site_id_invoice'];
		$siteDetail = $this->sites_model->get_site_detail($selectedSite, $user_id, $role_id);
		$invoiceUtilitySiteFlagMap = array(
			'electricity' => 'show_utility_electricity',
			'fuel_oil' => 'show_utility_fuel_oil',
			'lpg' => 'show_utility_lpg',
			'water' => 'show_utility_water',
			'natural_gas' => 'show_utility_natural_gas',
			'district_cooling' => 'show_utility_district_cooling',
			'district_heating' => 'show_utility_district_heating',
		);
		$enabledInvoiceUtilities = array();
		foreach ($invoiceUtilitySiteFlagMap as $invoiceUtilityKey => $siteFlagKey) {
			if (!array_key_exists($siteFlagKey, $siteDetail) || (int) $siteDetail[$siteFlagKey] === 1) {
				$enabledInvoiceUtilities[$invoiceUtilityKey] = true;
			}
		}
		$zipFileName = $siteDetail['site_location_name']. '_' . $year . '_invoices.zip';
		$zip = new ZipArchive();
		$basePath = 'uploads';

		$this->utilities_model->site_id = $selectedSite;
		$this->utilities_model->utilities_year = $year;
		$this->utilities_model->utilities_month = 12;
		$utilityData = $this->utilities_model->getSiteUtilityCurYear();
		$keyFile = 0;

		foreach ($utilityData as $key => $value) {
			$dateObj   = DateTime::createFromFormat('!m', $value['month_id']);
			$monthName = $dateObj->format('F');
			foreach ($value as $keyValue => $dataValue) {
				if (strpos($keyValue, '_invoice_scan') != false && !empty($dataValue) && isset($dataValue)) {
					$utilityname = trim(str_replace('_invoice_scan', '', $keyValue));
					if (!array_key_exists($utilityname, $enabledInvoiceUtilities)) {
						continue;
					}
					if (strpos($dataValue, ',') != false) {
						$dataexplode = explode(',', $dataValue);
						foreach ($dataexplode as $keyexplodedfile => $valueexplodedfile) {
							$filesToZip[$siteDetail['site_location_name']][$monthName][$utilityname][$keyFile] = site_url() . "assets/uploads/site_" . $selectedSite . "/utility_invoices/" . $valueexplodedfile;
							$keyFile++;
						}
					} else {
						$filesToZip[$siteDetail['site_location_name']][$monthName][$utilityname][$keyFile] = site_url() . "assets/uploads/site_" . $selectedSite . "/utility_invoices/" . $dataValue;
						$keyFile++;
					}
				}
			}
		}

		if(empty($filesToZip)) {
			$this->theme->set_message("No invoices found for the selected site and year.", 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites#group-report2');
			return;
		}

		if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
			$this->addArrayToZip($zip, $filesToZip, $basePath);
			// Close the ZIP archive
			$zip->close();
			if ($zipFileName) {
				ob_clean();
				// Prompt the user for download
				header('Content-Type: application/zip');
				header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
				header('Content-Length: ' . filesize($zipFileName));
				readfile($zipFileName);
			}
			$data_action = 'Update';
			$site_id = $_SESSION['admin']['site_id'];
			$user_id = $_SESSION['admin']['user_id'];
			saveAuditTrail($user_id, $site_id, 'Export Utility Invoices', $data_action);
			$this->theme->set_message("ZIP archive created successfully.", 'success');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites#group-report2');
			return;
		} else {
			$this->theme->set_message("Failed to create ZIP archive.", 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites#group-report2');
			return;
		}
	}

	function addArrayToZip($zip, $data, $basePath = '')
	{
		ini_set('memory_limit', '-1');
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				// If the value is an array, it represents a folder
				$folderPath = $basePath . '/' . $key;
				$zip->addEmptyDir($folderPath);
				// Recursively add the contents of the folder
				$this->addArrayToZip($zip, $value, $folderPath);
			} else {
				$folderPath = $basePath . '/' . $key;
				// If the value is not an array, it represents a file
				// Fetch the image content from the server URL
				$imageContent = file_get_contents($value);
				if ($imageContent !== false) {
					$zip->addFromString($folderPath.basename($value), $imageContent);
				} else {
					continue;
					// echo "Failed to fetch image from $value";
				}
			}
		}
	}

	
	public function export_utility_last_updated_log()
	{
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		ini_set('memory_limit', '-1');
		ini_set('MAX_EXECUTION_TIME', '-1');
		$this->load->model('countries/countries_model');

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP");
		//Start adding next sheets
		$columns = $sites = [];
		$site_types = $this->config->config['sites_type'];
		$areaUnit = [
			'm&#178;',
			'ft&#178;'
		];
		// Add new sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);
		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$objPHPExcel->getProperties()->setCreator("HEP")->setKeywords('Export Utility Update Logs');
		$objPHPExcel->getActiveSheet()->setTitle('Export Utility Update Logs');
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
		);

		$objPHPExcel->getDefaultStyle()->applyFromArray($style);

		// $sites = $this->sites_model->get_sites();

		$columns['site_location_name'] = 'Site Location Name';
		$columns['last_import_daily'] = 'Last Updated Import Daily Date';
		$columns['last_import_monthly'] = 'Last Updated Import Monthly Date';
		$columns['last_import_waste'] = 'Last Updated Import Waste Date';

		
		$user_id = $this->session->userdata[$this->section_name]['user_id'];
		$role_id = $this->session->userdata[$this->section_name]['role_id'];
		$sites = $this->sites_model->get_site_list_helper();

		$cells = array();
		$later1 = "";
		$later2 = 'A';
		$flag = 0;
		foreach ($columns as $key => $column) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . 1, $column);
			$cells[$key] = $later1 . $later2;
			$objPHPExcel->getActiveSheet()->getColumnDimension($later1 . $later2)->setWidth(15);
			if ($later2 == 'Z') {
				if ($flag == 0) {
					$later1 = 'A';
					$flag = 1;
				} else {
					$later1++;
				}
				$later2 = 'A';
			} else {
				$later2++;
			}
		}
		$moduleName = [
			'last_import_daily' => 'Import (Daily)',
			'last_import_monthly' => 'Import (Monthly)',
			'last_import_waste' => 'Import Waste'
		];
		$row = 2;
		if (isset($sites) && !empty($sites)) {
			foreach ($sites as $keySite => $site) {
				$siteDetail = [];
				$siteDetail['site_location_name'] = $site;
				foreach ($moduleName as $moduleKey => $moduleValue) {
					$queryResult = $this->sites_model->get_site_last_import_date($keySite, $moduleValue);
					$queryResult = (array) $queryResult;
					$siteDetail[$moduleKey] = (isset($queryResult) && isset($queryResult['Date']) && !empty($queryResult['Date'])) ? $queryResult['Date'] : '';
				}
				if (isset($siteDetail) && !empty($siteDetail)) {
					foreach ($siteDetail as $key => $val) {
						if (!empty($val) && is_numeric($val)) {
							$val = round($val, 4);
							if ($val == 0) {
								$val = '';
							}
						}
						if (!is_string($val) && !is_numeric($val)) {
							$val = '';
						}
						if ($val === '0') {
							$val = '';
						}
						if (array_key_exists($key, $cells) && isset($val) && !empty($val)) {
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
						} else {
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, '-');
						}
					}
				}
				$row++;
			}
		}
		$data_action = 'Export';
		$site_id = $_SESSION['admin']['site_id'];
		$user_id = $_SESSION['admin']['user_id'];
		saveAuditTrail($user_id, $site_id, 'Export Utility Update Logs', $data_action);
		header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Export Utility Update Logs.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	public function group_utility_report($postMonth) {
		[$m, $y] = array_pad(explode('-', $postMonth), 2, null);
		$currMonth   = (int) $m;
		$currentYear = (int) $y;
	    $baseDate = new DateTime(sprintf('%04d-%02d-01', $currentYear, $currMonth));
		$prevDate  = (clone $baseDate)->modify('-1 month');
		$prevMonth = (int) $prevDate->format('m');
		$prevYear  = (int) $prevDate->format('Y');
	    $lastYear = (int) (clone $baseDate)->modify('-1 year')->format('Y');
		$this->load->model('sites/sites_model');
		$objWriter = $this->sites_model->generateGroupUtilityReport($currentYear,$currMonth,$prevYear,$prevMonth,$lastYear);
		if ($objWriter === false) {
			$this->theme->set_message('No hotels are ticked for monthly reporting. The Group Utility Report cannot be generated.', 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
		}
		exit;
	}

	public function export_group_waste_corporate_report($postMonth)
	{
		$postData = [];
		$postData['month'] = explode('-', $postMonth)[0] ?? null;
		$postData['year'] = explode('-', $postMonth)[1] ?? null;
		ob_end_clean();
		ob_start();
		$decimal_places = 2;
		$currentYear = isset($postData) && isset($postData['year']) ? (int) $postData['year'] : date('Y');
		$currMonth = isset($postData) && isset($postData['month']) ? (int) $postData['month'] : (int) date('n') - 1;
		$lastYear = $currentYear - 1;
		$prevMonth = $currMonth - 1 ?: 12;
		$prevYear  = ($prevMonth == 12) ? $currentYear - 2 : $currentYear - 1;
		if ($currMonth == 0) {
			$currMonth = 12;
			$currentYear = $currentYear - 1;
			$lastYear = $lastYear - 1;
		}
		$this->load->model([
			'sites/sites_model',
			'sites/site_waste_model'
		]);
		$parentHeading = [
			'Total waste generated (kg)',
			'Organic / food waste (kg)',
			'Recyclables (kg)',
			'Hazardous waste (kg)',
			'Waste diverted from landfill (%)',
			'Waste per occupied room (kg)',
			'Organic waste per guest (kg)',
			'Recyclables per guest (kg)',
			'Rebates'
		];
		$childLabels = [
			$lastYear,
			$currentYear,
			'% Variance'
		];
		$columns = [
			'Hotel Name',
			'Total waste generated (kg) '.$lastYear,
			'Total waste generated (kg) '.$currentYear,
			'Total waste generated (kg) % Variance',

			'Organic / food waste (kg) '.$lastYear,
			'Organic / food waste (kg) '.$currentYear,
			'Organic / food waste (kg) % Variance',

			'Recyclables (kg) '.$lastYear,
			'Recyclables (kg) '.$currentYear,
			'Recyclables (kg) % Variance',

			'Hazardous waste (kg) '.$lastYear,
			'Hazardous waste (kg) '.$currentYear,
			'Hazardous waste (kg) % Variance',

			'Waste diverted from landfill (%) '.$lastYear,
			'Waste diverted from landfill (%) '.$currentYear,
			'Waste diverted from landfill (%) % Variance',

			'Waste per occupied room (kg) '.$lastYear,
			'Waste per occupied room (kg) '.$currentYear,
			'Waste per occupied room (kg) % Variance',

			'Organic waste per guest (kg) '.$lastYear,
			'Organic waste per guest (kg) '.$currentYear,
			'Organic waste per guest (kg) % Variance',

			'Recyclables per guest (kg) '.$lastYear,
			'Recyclables per guest (kg) '.$currentYear,
			'Recyclables per guest (kg) % Variance',

			'Rebates '.$lastYear,
			'Rebates '.$currentYear,
			'Rebates % Variance',
		];
		require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
		$this->lang->load('sites/sites', 'english'); 
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("HEP")
			->setTitle("Group Waste Corporate Report")
			->setKeywords("Group Waste Corporate Report");

		$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
		$highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();
		$highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetTitle = date('F Y', strtotime($currentYear . '-' . $currMonth . '-01'));
		$objPHPExcel->getActiveSheet()->setTitle('Month - ' . $sheetTitle);
		applyHeaderColorsWaste($objPHPExcel->getActiveSheet());
		autoSizeColumns($objPHPExcel->getActiveSheet());
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Hotel Name');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
		$startCol = 1;
		foreach ($parentHeading as $heading) {
			$colStart = PHPExcel_Cell::stringFromColumnIndex($startCol);
			$colEnd   = PHPExcel_Cell::stringFromColumnIndex($startCol + 2);

			$objPHPExcel->getActiveSheet()->setCellValue($colStart . '1', $heading);
			$objPHPExcel->getActiveSheet()->mergeCells($colStart . '1:' . $colEnd . '1');

			$startCol += 3;
		}
		$colIndex = 0;
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setCellValue('A2', '');
		$startCol = 1;
		foreach ($parentHeading as $heading) {
			foreach ($childLabels as $label) {
				$sheet->setCellValueByColumnAndRow($startCol, 2, $label);
				$startCol++;
			}
		}
		$rowNum = 3;
		$data = $rowsWasteData = [];
		$sites = $this->sites_model->get_site_detail_multiple();
		usort($sites, function ($a, $b) {
			return strcasecmp($a['site_location_name'], $b['site_location_name']);
		}); 

		if (!empty($sites)) {
			foreach ($sites as $key => $site_detail) {
				if(empty($site_detail['id'])) {
					continue;
				}
				$dataWaste = $getUtilities = $dataCal = [];
				$site_id = $site_detail['id'];
				$data['sites'][$site_id]['site_location_name'] = $site_detail['site_location_name'];

				$this->load->model('utilities/utilities_model');
				$util = $this->utilities_model;
				$util->site_id = $site_id;
				$util->utilities_month = $currMonth;
				$util->utilities_year  = $currentYear;
				$getUtilities = $util->getUtility();
				
				$dataCal['waste']['total_room_night'] = (float) ($getUtilities['total_room_night'] ?? 0);
				$dataCal['waste']['total_guests'] = (float) ($getUtilities['total_guests'] ?? 0);
				$dataWaste = $this->site_waste_model->getWasteReportData($site_id, $dataCal['waste'], $currentYear, $currMonth);
			
				$wasteReportMap = [];

				if (!empty($dataWaste['wasteReport']) && is_array($dataWaste['wasteReport'])) {
					foreach ($dataWaste['wasteReport'] as $row) {
						if (isset($row['metric'])) {
							$wasteReportMap[$row['metric']] = $row;
						}
					}
				}

				$wastePerGuestMap = [];

				if (!empty($dataWaste['wastePerGuest']) && is_array($dataWaste['wastePerGuest'])) {
					foreach ($dataWaste['wastePerGuest'] as $row) {
						if (isset($row['metric'])) {
							$wastePerGuestMap[$row['metric']] = $row;
						}
					}
				}
				$rebates = [];
				if (!empty($dataWaste['rebates']) && is_array($dataWaste['rebates'])) {
					$rebates[$dataWaste['rebates']['metric']] = $dataWaste['rebates'];
				}

				foreach ($columns as $column) {

					/* ================= HOTEL NAME ================= */
					if ($column === 'Hotel Name') {
						$rowsWasteData[$site_id][$column] = $site_detail['site_location_name'];
						continue;
					}

					$metric = null; 

					switch (true) {

						case strpos($column, 'Total waste generated (kg)') !== false :
							$metric = 'Total waste generated (kg)';
							break;

						case strpos($column, 'Organic / food waste (kg)') !== false :
							$metric = 'Organic / food waste (kg)';
							break;

						case strpos($column, 'Recyclables (kg)') !== false :
							$metric = 'Recyclables (kg)';
							break;

						case strpos($column, 'Hazardous waste (kg)') !== false :
							$metric = 'Hazardous waste (kg)';
							break;

						case strpos($column, 'Waste diverted from landfill (%)') !== false :
							$metric = 'Waste diverted from landfill (%)';
							break;

						case strpos($column, 'Waste per occupied room (kg)') !== false :
							$metric = 'Waste (kg/Room night)';
							break;

						case strpos($column, 'Organic waste per guest (kg)') !== false :
							$metric = 'Organic waste (kg/Guest Night)';
							break;

						case strpos($column, 'Recyclables per guest (kg)') !== false :
							$metric = 'Recyclables (kg/Guest Night)';
							break;

						case strpos($column, 'Rebates') !== false :
							$metric = 'Rebates';
							break;

						default:
							$rowsWasteData[$site_id][$column] = '';
							continue 2;
					}

					/* ================= VALUE RESOLUTION ================= */

					if (isset($wasteReportMap[$metric])) {
						$source = $wasteReportMap[$metric];
						$rowsWasteData[$site_id][$column] =
							(strpos($column, '% Variance') != false)
								? $source['change']
								: ((strpos(trim($column), trim((string)$lastYear)) != false)
									? $source['previous']
									: $source['current']);

					} elseif (isset($wastePerGuestMap[$metric])) {
						$source = $wastePerGuestMap[$metric];
						$rowsWasteData[$site_id][$column] =
							(strpos($column, '% Variance') != false)
								? $source['value']
								: ((strpos(trim($column), trim((string)$lastYear)) != false)
									? $source['previous']
									: $source['current']);
					} else {
						$rowsWasteData[$site_id][$column] = '';
					}
					if (isset($rebates[$metric])) {
						$source = $rebates[$metric];
						$rowsWasteData[$site_id][$column] =
							(strpos($column, '% Variance') != false)
								? $source['value']
								: ((strpos(trim($column), trim((string)$lastYear)) != false)
									? $source['previous']
									: $source['current']);
					}
				}

			}
		}
		foreach ($rowsWasteData as $row) {
			$colIndex = 0;
			foreach ($columns as $col) {
				$objPHPExcel->getActiveSheet()
					->setCellValueByColumnAndRow($colIndex++, $rowNum, $row[$col]);
			}
			$rowNum++;
		}

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow0 = $sheet->getHighestRow();
		$highestColumn0 = $sheet->getHighestColumn();
		$sheet->setAutoFilter("A2:{$highestColumn0}{$highestRow0}");
		$sheet->getStyle("B3:{$highestColumn0}{$highestRow0}")
			->getAlignment()
			->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("B3:{$highestColumn0}{$highestRow0}")
			->getNumberFormat()
			->setFormatCode('#,##0.00');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Group Waste Corporate Report.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		sanitize_report_spreadsheet($objPHPExcel);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * Download the Utilities Management Dashboard PDF(s) for last month
	 * (files written this month by the corporate report cron).
	 */
	public function download_corporate_utilities_dashboard()
	{
		$role_id = isset($this->session->userdata[$this->section_name]['role_id'])
			? (int) $this->session->userdata[$this->section_name]['role_id']
			: 0;
		$username = isset($this->session->userdata[$this->section_name]['username'])
			? strtolower($this->session->userdata[$this->section_name]['username'])
			: '';

		if (!in_array($role_id, array(1, 6), true) || $username === '') {
			$this->theme->set_message(lang('permission-not-allowed'), 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
			return;
		}

		$cronDir = BASE_PATH_CUSTOM . '/assets/uploads/cron/';
		if (!is_dir($cronDir)) {
			$this->theme->set_message('No corporate dashboard reports are available yet.', 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
			return;
		}

		$pattern = $cronDir . $username . '*upper_management_report*.pdf';
		$files = glob($pattern);
		if (empty($files)) {
			// Fallback: any upper management PDF for this user prefix
			$files = glob($cronDir . $username . '*.pdf');
		}

		if (empty($files)) {
			$this->theme->set_message('No Utilities Management Dashboard PDF found for your account. It will appear here after the next corporate report cron run.', 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
			return;
		}

		// Cron writes last-month data during the current month. Ignore older
		// runs (e.g. July files that still show June) so this download matches last month.
		$startOfThisMonth = strtotime(date('Y-m-01 00:00:00'));
		$currentMonthFiles = array_values(array_filter($files, function ($file) use ($startOfThisMonth) {
			return filemtime($file) >= $startOfThisMonth;
		}));
		if (empty($currentMonthFiles)) {
			$lastMonthLabel = date('F Y', strtotime('first day of last month'));
			$this->theme->set_message('The Utilities Management Dashboard for ' . $lastMonthLabel . ' is not available yet. It is created when the corporate report cron runs this month.', 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
			return;
		}
		$files = $currentMonthFiles;

		usort($files, function ($a, $b) {
			return filemtime($b) - filemtime($a);
		});

		// Prefer today's regional dashboards; otherwise newest file
		$latestMtime = filemtime($files[0]);
		$latestFiles = array_values(array_filter($files, function ($file) use ($latestMtime) {
			return filemtime($file) >= ($latestMtime - 60);
		}));

		if (count($latestFiles) === 1) {
			$file = $latestFiles[0];
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . basename($file) . '"');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		}

		$zipPath = $cronDir . 'corporate_utilities_dashboard_' . $username . '_' . date('YmdHis') . '.zip';
		$zip = new ZipArchive();
		if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			$this->theme->set_message('Unable to prepare corporate dashboard download.', 'error');
			redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'reports/sites');
			return;
		}
		foreach ($latestFiles as $file) {
			$zip->addFile($file, basename($file));
		}
		$zip->close();

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . basename($zipPath) . '"');
		header('Content-Length: ' . filesize($zipPath));
		readfile($zipPath);
		@unlink($zipPath);
		exit;
	}
}
