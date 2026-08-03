<?php



/**

 *  User Controller (Front)

 *

 *  To perform login,registration and forgot password process.

 *

 * @package CIDemoApplication

 * @subpackage Survey

 * @copyright	(c) 2013, TatvaSoft

 * @author panks

 */

class Survey_admin extends Base_Admin_Controller {



    public $search_term,$search_section;

    function __construct() {

	parent::__construct();

	$this->load->model('survey/survey_model');

	$this->load->model('survey/survey_questions_answer_model');
	$this->load->model('survey/survey_access_model');
	$this->load->library('form_validation');

	$this->load->helper('url');

	$this->access_control($this->access_rules());

	$this->user_id = isset($this->session->userdata[$this->section_name]['user_id'])?$this->session->userdata[$this->section_name]['user_id']:0;

	$this->role_id = isset($this->session->userdata[$this->section_name]['role_id'])?$this->session->userdata[$this->section_name]['role_id']:0;

	$this->site_id = isset($this->session->userdata[$this->section_name]['site_id'])?$this->session->userdata[$this->section_name]['site_id']:0;



    }



    /**

     * function accessRules to check page access

     */

    private function access_rules() {

	return array(

	    array(
		'actions' => array('index', 'action', 'add', 'edit','survey_form', 'delete','open','delete_survey_form_image', 'sortsave', 'export', 'exportone', 'exportarchieveone', 'exportarchieve'),
		'users' => array('@'),

	    )

	);

    }



    /**

     * Function index to view listing of survey

     */

    public function index()

    {

	//Paging parameters

	$offset                             = get_offset($this->page_number, $this->record_per_page);

	$this->survey_model->record_per_page = $this->record_per_page;

	$this->survey_model->offset          = $offset;



	//set sort/search parameters in pagging

	if ($this->input->post()) {

	    $data = $this->input->post();
	    // Search Term ***

	    if (isset($data['search_term']) && !empty($data['search_term'])) {

		$this->survey_model->search_term = trim($data['search_term']);

		$this->session->set_custom_userdata($this->section_name, "survey_search_term", $this->input->post('search_term'));

	    } else {

		$this->session->set_custom_userdata($this->section_name, "survey_search_term", "");

	    }



	    if ($data['search_section'] >= 0) {

		$sections = explode('|', QUESTION_SECTIONS);

		$this->survey_model->search_section = $sections[$data['search_section']];

		$this->session->set_custom_userdata($this->section_name, "search_section", $this->input->post('search_section'));

	    } else {

		$this->session->set_custom_userdata($this->section_name, "search_section", "");

	    }



	    if ($data['search_source'] >= 0) {

		$sources = explode('|', QUESTION_SOURCES);

		$this->survey_model->search_source = $sources[$data['search_source']];

		$this->session->set_custom_userdata($this->section_name, "search_source", $this->input->post('search_source'));

	    } else {

		$this->session->set_custom_userdata($this->section_name, "search_source", "");

	    }

	    // Search Term ***

	    // Sort Order ***

	    if (isset($data['sort_by']) && $data['sort_order']) {

		$this->survey_model->sort_by    = $data['sort_by'];

		$this->survey_model->sort_order = $data['sort_order'];

		$this->session->set_custom_userdata($this->section_name, "survey_sort_by", $this->input->post('sort_by'));

		$this->session->set_custom_userdata($this->section_name, "survey_sort_order", $this->input->post('sort_order'));

	    } else {

		$this->session->set_custom_userdata($this->section_name, "survey_sort_by", "");

		$this->session->set_custom_userdata($this->section_name, "survey_sort_order", "");

	    }

	    // Sort Order ***

	}



	if (!empty($this->session->userdata[$this->section_name]['survey_search_term'])) {

	    $this->survey_model->search_term = trim($this->session->userdata[$this->section_name]['survey_search_term']);

	}

	if (!empty($this->session->userdata[$this->section_name]['survey_sort_by'])) {

	    $this->survey_model->sort_by = $this->session->userdata[$this->section_name]['survey_sort_by'];

	}

	if (!empty($this->session->userdata[$this->section_name]['survey_sort_order'])) {

	    $this->survey_model->sort_order = $this->session->userdata[$this->section_name]['survey_sort_order'];

	}



	//Load data for url listing
	$this->survey_model->year_id = date('Y');
	$surveys                            = $this->survey_model->get_survey_listing();

	$this->survey_model->_record_count = true;
	$this->survey_model->year_id = date('Y');
	$total_records                    = $this->survey_model->get_survey_listing();



	// Pass data to view file

	$this->search_term     = $this->survey_model->search_term;

	$data['surveys']       = $surveys;

	$data['page_number']   = $this->page_number;

	$data['total_records'] = $total_records;

	$data['search_term']   = $this->survey_model->search_term;

	$data['sort_by']       = $this->survey_model->sort_by;

	$data['sort_order']    = $this->survey_model->sort_order;

	$data['search_section']= $this->survey_model->search_section;

	$data['search_source'] = $this->survey_model->search_source;

	$data['sections']      = explode('|', QUESTION_SECTIONS);

	$data['sources']      = explode('|', QUESTION_SOURCES);

	//Create page-title

	$this->theme->set('page_title', lang('survey-management'));



	//Render view

	$this->theme->view($data);

    }



    /**

     * action to add/edit survey question page

     * @param string $action : add or edit

     * @param string $id : if in edit mode

     */

    function action($action, $id = 0) {

	//Type Casting

	$action = trim(strip_tags($action));

	$id = intval($id);



	if ($this->input->post('questionsubmit')) {

	    $postData = $this->input->post();

	    $this->load->library('form_validation');

	    $this->form_validation->set_rules('question_text', lang('question_text'), 'trim|required');

	    $this->form_validation->set_rules('question_type', lang('question_type'), 'trim|required');

	    $this->form_validation->set_rules('question_description', lang('question_description'), 'trim');

	    $this->form_validation->set_rules('section', lang('section'), 'trim|required');
	    $this->form_validation->set_rules('required', lang('required'), 'trim|required');
	    $this->form_validation->set_rules('source', lang('source'), 'trim|required');

	    if (!isset($id)) {
		$this->form_validation->set_rules('question_text', lang('question_text'), 'is_unique[survey_question.question_text]');
	    }

	    if ($postData['question_type'] != 2 && $postData['question_type'] != 3 && $postData['question_type'] != 5) {

		$this->form_validation->set_rules('question_options', lang('question_options'), 'required');

	    }

	    $question_types = explode(',', QUESTION_TYPES);

	    $question_sections = explode('|', QUESTION_SECTIONS);

	    $question_sources = explode('|', QUESTION_SOURCES);

	    $postDataKey['question_type'] = $postData['question_type'];

	    $postDataKey['section'] = $postData['section'];

	    $postDataKey['source'] = $postData['source'];

	    $postData['question_type'] = strtolower($question_types[$postData['question_type']]);

	    $postData['section'] = strtolower($question_sections[$postData['section']]);

	    $postData['source'] = strtolower($question_sources[$postData['source']]);

	    $postData['question_options'] = ($postData['question_type'] != 'file' && $postData['question_type'] != 'textarea' && $postData['question_type'] != 'textbox') ? $postData['question_options'] : '';
	    $postData['year_id'] = date('Y');
	    $this->load->model('survey/survey_model');
	    $this->survey_model->question_text = $postData['question_text'];

	    $this->survey_model->question_type = $postData['question_type'];
	    $this->survey_model->question_options = ($postData['question_type'] != 'textarea' && $postData['question_type'] != 'textbox') ? $postData['question_options'] : '';
	    $this->survey_model->required = ($postData['required']) ? $postData['required'] : '';
	    $this->survey_model->source = $postData['source'];

	    $this->survey_model->question_description = $postData['question_description'];

	    $this->survey_model->section = $postData['section'];
	    $this->survey_model->is_upload = $postData['is_upload'] ? 1 : 0;
	    $this->survey_model->year_id = $postData['year_id'] ? $postData['year_id'] : NULL;

	    if ($this->form_validation->run($this) == true) {

		$this->survey_model->insert_survey_question($id);

		if ($id > 0) {

		    $this->theme->set_message(lang('msg_update_success'), 'success');

		} else {

		    $this->theme->set_message(lang('msg_add_success'), 'success');

		}

		redirect(BASE_ADMIN_URL_CUSTOM . 'survey');

	    }

	}



	if (!$this->input->post()) {

	    if (isset($id) && $id != '' && $id != '0') {

		$survey_question_result = $this->survey_model->get_survey_question_detail_by_id($id);

		$survey_question = $survey_question_result[0];

	    }

	} else {

	    $survey_question = $this->input->post();

	}



	if ($action == 'add' || $action == 'edit') {

	    // Breadcrumb settings

	    $this->breadcrumb->add(lang('survey_question_management'), base_url(). 'survey');

	    if ($action == "add") {

		$this->theme->set('page_title', $this->lang->line('add_survey_question'));

		$this->breadcrumb->add($this->lang->line('add_survey_question'));

		$id = '';

	    } elseif ($action == "edit") {

		$this->theme->set('page_title', $this->lang->line('edit_survey_question'));

		$this->breadcrumb->add($this->lang->line('edit_survey_question'));

	    }



	    //Variable assignments to view

	    $data = array();

	    $data['action'] = $action;

	    $data['id'] = $id;

	    $data['csrf_token'] = $this->security->get_csrf_token_name();

	    $data['csrf_hash'] = $this->security->get_csrf_hash();

	    $data['survey_question'] = $survey_question;

	    //Render view

	    $this->theme->view($data, 'admin_add');

	} else {

	    $this->theme->set_message(lang('permission_not_allowed'), 'error');

	    redirect(BASE_ADMIN_URL_CUSTOM . 'survey');

	    exit;

	}

    }



    public function delete()

    {
	$data = $this->input->post();

	$id   = intval(base64_decode($data['id']));

	$result  = $this->survey_model->get_survey_question_detail_by_id($id);

	if (!empty($result)) {

	    $res = $this->survey_model->delete_survey($id);

	    if ($res) {

		echo $this->theme->message(lang('survey-delete-success'), 'success');

	    }

	} else {

	    echo $this->theme->message(lang('invalid-id-msg'), 'error');

	}

    }



    public function survey_form()

    {
	// if(CheckIfSurveyIsOpen() == 1 || $this->session->userdata[get_current_section($this, true)]['user_id'] == 1) {
	    $this->survey_model->year_id = date('Y');
	    $survey_questions_form_render = $this->survey_model->get_survey_listing();
	    $questionData = array();
	    $is_survey_open = CheckIfSurveyIsOpen() == 1 || $this->session->userdata[get_current_section($this, true)]['user_id'] == 1 ? 1 : 0;
	    foreach ($survey_questions_form_render as $key => $value) {
		$survey_question_answer = [];
		if (isset($value['s']['question_id']) && !empty($value['s']['question_id'])) {
		    if (isset($this->site_id) && $this->site_id != '' && $this->site_id != '0') {
			$this->survey_questions_answer_model->question_id = $value['s']['question_id'];
			// Get current years survey response if Exist
			$this->survey_questions_answer_model->year = date('Y');
			$survey_question_answer = $this->survey_questions_answer_model->get_survey_answer();
			if(!$survey_question_answer['count']) {
			    // Get past years survey response if Exist
			    $this->survey_questions_answer_model->year = date('Y') - 1;
			    $survey_question_answer = $this->survey_questions_answer_model->get_survey_answer();
			    if(!$survey_question_answer['count']) {
				$survey_question_answer = $this->input->post();
			    }
			}
		    }
		    if ($survey_question_answer['count']) {
			$value['s']['questions_answer'] = $survey_question_answer['data'][0]['s']['question_answer'];
			$value['s']['questions_upload'] = $survey_question_answer['data'][0]['s']['question_upload'];
			$value['s']['survey_questions_answer_id'] = $survey_question_answer['data'][0]['s']['survey_questions_answer_id'];
		    }
		    // $value['s']['if_survey_is_open'] = $is_survey_open;
		    $questionData[$value['s']['section']][] = $value; // get all record against each user
		}
	    }

	    // Sort array according to sort_order_number
	    foreach ($questionData as $key => $value) {
		$sectionWiseSortedData = [];
		$sectionWiseData = $value;
		foreach ($sectionWiseData as $keysection => $sectionValue) {
		    if(isset($sectionValue['s']['sort_order_number']) && !empty($sectionValue['s']['sort_order_number'])){
			$sectionWiseSortedData[($sectionValue['s']['sort_order_number']) - 1]['s'] = $sectionValue['s'];
			ksort($sectionWiseSortedData);
			$questionData[$key] = $sectionWiseSortedData;
		    } else {
			$questionData[$key][$keysection] = $sectionValue;
		    }
		}
	    }

	    if ($this->input->post('surveyFormSave') || $this->input->post('surveyFormSubmit')) {
		$postData = $this->input->post();
		if($this->input->post('surveyFormSubmit')) {
		    foreach ($survey_questions_form_render as $key => $value) {
			if($value['s']['required'] == 'Yes') {
			    if($value['s']['question_type'] == 'multiselect' || $value['s']['question_type'] == 'checkbox') {
				$this->form_validation->set_rules('survey_question_'.$value['s']['question_id'].'[]', $value['s']['question_text'], 'trim|required');
			    } else {
				$this->form_validation->set_rules('survey_question_'.$value['s']['question_id'], $value['s']['question_text'], 'trim|required');
			    }

			    }

		    }
		    if ($this->form_validation->run($this) == true) {
			$this->saveAnswerLogic($postData,'surveyFormSubmit');
		    } else {
			$this->theme->set_message('Some required answers are missing. Please check all sections.', 'error');
		    }
		} else if($this->input->post('surveyFormSave')) {
		    $this->saveAnswerLogic($postData,'surveyFormSave');
		}
	    }

	    $this->theme->set('page_title', $this->lang->line('survey_form_data'));

	    $this->breadcrumb->add($this->lang->line('survey_form_data'));

	    //Variable assignments to view

	    $data = array();

	    $data['csrf_token'] = $this->security->get_csrf_token_name();

	    $data['csrf_hash'] = $this->security->get_csrf_hash();
	    $sortKey = array('property','energy and carbon','water','waste','health and environment','sourcing and F and B','community and social','residences');
	    $orderedArray = array();
	    foreach ($sortKey as $key) {
		$orderedArray[$key] = $questionData[$key];
	    }
	    $data['if_survey_is_open'] = $is_survey_open;
	    $data['survey_questions_form_render'] = $orderedArray;
	    //Render view
	    $this->theme->view($data, 'survey_form');

	// } else {
	//     if(CheckIfSurveyIsOpen() == 2) {
	//         $this->theme->set_message($this->lang->line('survey-needs-to-be-opened'), 'error');
	//         redirect(site_url() .BASE_ADMIN_URL_CUSTOM.'dashboard');
	//         exit;
	//     } else {
	//         $this->theme->set_message($this->lang->line('permission-not-allowed'), 'error');
	//         redirect(site_url() .BASE_ADMIN_URL_CUSTOM.'dashboard');
	//         exit;
	//     }

	// }
    }

    // delete_survey_form_image
    function delete_survey_form_image()
    {
	$questionId = intval($this->input->post('questionId'));
	$questionAnswerId = intval($this->input->post('questionAnswerId'));
	$imageName = $this->input->post('imageName');

	//logic
	if ($questionId != 0 && $questionId != '' && is_numeric($questionId) && $questionAnswerId != 0 && $questionAnswerId != '' && is_numeric($questionAnswerId))
	{
	    $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
	    $data['deleted_at'] = GetCurrentDateTime();
	    $data['deleted_by'] = $user_id;
	    $data['question_id'] = $questionId;
	    $data['survey_questions_answer_id'] = $questionAnswerId;
	    $data['question_upload'] = $imageName;
	    if($this->survey_questions_answer_model->delete_survey_image($data))
	    {
		$message = $this->theme->message(lang('msg_delete_success'), 'success');
	    }
	    else
	    {
		$message = $this->theme->message(lang('invalid_id_msg'), 'error');
	    }
	}
	else
	{
	    $message = $this->theme->message(lang('invalid_id_msg'), 'error');
	}
	echo $message;
    }

    function open()
    {
	if ($this->input->post()) {
	    $this->form_validation->set_rules('close_date', lang('close_date'), 'trim|required');
	    $data = $this->input->post();
	    if ($this->form_validation->run($this) == true) {
		$data['close_date'] = date('Y-m-d', strtotime($data['close_date']));
		if(!CheckIfSurveyIsOpen()) {
		    $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
		    $close_date = $data['close_date'];
		    $this->survey_access_model->user_id = $user_id;
		    $this->survey_access_model->close_date = $close_date;
		    $this->survey_access_model->insert_survey_access();

		    $this->theme->set_message(lang('msg_open_survey_success'), 'success');
		    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/index');
		} else if (CheckIfSurveyIsOpen() == 2) {
		    $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
		    $this->survey_access_model->deleteExistingEntry($user_id);
		    $close_date = $data['close_date'];
		    $this->survey_access_model->user_id = $user_id;
		    $this->survey_access_model->close_date = $close_date;
		    $this->survey_access_model->insert_survey_access();

		    $this->theme->set_message(lang('msg_open_survey_success'), 'success');
		    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/index');
		} else {
		    $this->theme->set_message(lang('msg_survey_already_open'), 'error');
		    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/index');
		}
	    } else {
		$this->theme->set_message(lang('close_date'), 'error');
		redirect(BASE_ADMIN_URL_CUSTOM . 'survey');
		exit;
	    }
	    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/index');
	}
    }

    public function sortsave(){
	if ($this->input->post()) {
	    $data = $this->input->post();
	    foreach ($data['ids'] as $key => $value) {
		$sortNumber = $key+1;
		$question_sections = explode('|', QUESTION_SECTIONS);
		$section = strtolower($question_sections[$data['section']]);
		$order_number_id = displayLabelOnSurveyForm($section, $sortNumber);
		$this->survey_model->updateOrderNumber($order_number_id, $value);
		$this->survey_model->updateSortOrderNumber($sortNumber, $value);
	    }
	}
	return true;
    }

    public function export()
    {
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Survey Data")
	    ->setKeywords("Survey Data");

	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'FFF2CC')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('I1:LL1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'E2EFDA')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('I2:LL2')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');
	$objPHPExcel->getActiveSheet()->getStyle('I3:LL3')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->setAutoFilter(
	    $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
	);
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$columns["attribute"] = 'Attribute';
	$columns["property_name"] = 'Property Name';
	$columns["country"] = 'Country';
	$columns["region"] = 'Region';
	$columns["property_type"] = 'Property Type';
	$columns["last_update_by"] = 'Last update by';
	$columns["last_update_date"] = 'Last update date';
	$columns["status"] = 'Status';

	// Section wise sorted order question display
	$sections = explode('|', QUESTION_SECTIONS);
	$surveySorted = [];
	for($i=0; $i<=7; $i++) {
	    $this->survey_model->year_id = date('Y');
	    $this->survey_model->search_section = $sections[$i];
	    $surveys = $this->survey_model->get_survey_listing();
	    $surveySorted[$sections[$i]] =  $surveys;
	}

	// Mapping question and answer details
	foreach ($surveySorted as $key => $value) {
	    foreach ($value as $sectionwisevalue) {
		$columns['question_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_text'];
		if($sectionwisevalue['s']['is_upload'] == 1) {
		    $columnsUpload['question_upload_'.$sectionwisevalue['s']['question_id']] = "Upload ".$sectionwisevalue['s']['order_number'];
		}
		$columnsRow2['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['order_number'];
		$columnsRow3['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_options'];
	    }
	}
	foreach ($columnsUpload as $key => $value) {
	    $columns[$key] = $value;
	}

	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
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
	//Row2 to display options
	$row = 2;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'I';
	$flag = 0;
	foreach ($columnsRow2 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow2[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	//Row3 to display identical numbers
	$row = 3;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'I';
	$flag = 0;
	foreach ($columnsRow3 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow3[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	$row = 4;
	$this->survey_model->year_id = date('Y');
	$dataArray = $this->survey_model->getExportArray();
	$status = [];
	// Set audit log values for each survey
	foreach ($dataArray as $key => $value) {
	    $statusText = '';
	    if($value['status'] == 1) {
		$statusText = 'Saved';
	    } else if ($value['status'] == 2 || $value['status'] == 3) {
		$statusText = 'Submitted';
	    }
	    if(!isset($status[$value['site_id']])) {
		$status[$value['site_id']] = $statusText;
	    }
	    $audit_detail = $this->survey_model->getLatestAuditSurveyDetail($value['site_id']);
	    $dataArray[$key]['last_update_date'] = (isset($audit_detail['last_update_date']) && strpos($audit_detail['last_update_date'], date('Y')) !== false) ? $audit_detail['last_update_date'] : '';
	    $dataArray[$key]['last_update_by'] = $audit_detail['last_update_by'];
	    unset($dataArray[$key]['status']);
	}
	$siteWiseData = $tempUploadDataColumn = [];
	foreach ($dataArray as $key => $value) {
	    $siteWiseData[$value['site_id']]['attribute'] = $value['attribute'];
	    $siteWiseData[$value['site_id']]['property_name'] = $value['site_location_name'];
	    $siteWiseData[$value['site_id']]['country'] = $value['country'];
	    $siteWiseData[$value['site_id']]['region'] = $value['region_name'];
	    $siteWiseData[$value['site_id']]['property_type'] = $value['property_type'];
	    $siteWiseData[$value['site_id']]['last_update_by'] = $value['last_update_by'];
	    $siteWiseData[$value['site_id']]['last_update_date'] = $value['last_update_date'];
	    $siteWiseData[$value['site_id']]['status'] = $status[$value['site_id']];
	    if(isset($value['question_id'])) {
		$siteWiseData[$value['site_id']]['question_'.$value['question_id']] = html_entity_decode(html_entity_decode(html_entity_decode(html_entity_decode($value['question_answer']))));
		if(isset($value['is_upload']) && isset($value['question_upload']) && !empty($value['question_upload'])) {
		    $siteWiseData[$value['site_id']]['question_upload_'.$value['question_id']] = site_url() . "assets/uploads/" .$value['question_upload'];
		}
	    }
	    unset($dataArray[$key]['site_id']);
	    unset($dataArray[$key]['question_answer']);
	    unset($dataArray[$key]['question_id']);
	}
	foreach ($siteWiseData as $data) {
	    foreach ($data as $key => $val) {
		if (array_key_exists($key, $cells)) {
		    if(strpos($key, 'question_upload_') !== false) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($cells[$key])->setAutoSize(true);
		    }
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}

	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Survey Data.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }

    public function exportarchieve()
    {
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Survey Data")
	    ->setKeywords("Survey Data");

	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'FFF2CC')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H1:LL1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'E2EFDA')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H2:LL2')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');
	$objPHPExcel->getActiveSheet()->getStyle('H3:LL3')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');
	$objPHPExcel->getActiveSheet()->getStyle('H4:LL4')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->setAutoFilter(
	    $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
	);
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$columns["attribute"] = 'Attribute';
	$columns["property_name"] = 'Property Name';
	$columns["country"] = 'Country';
	$columns["region"] = 'Region';
	$columns["property_type"] = 'Property Type';
	$columns["last_update_by"] = 'Last update by';
	$columns["last_update_date"] = 'Last update date';

	// Section wise sorted order question display
	$sections = explode('|', QUESTION_SECTIONS);
	$surveySorted = [];
	for($i=0; $i<=7; $i++) {
	    $this->survey_model->year_id = date('Y') - 1;
	    $this->survey_model->search_section = $sections[$i];
	    $surveys = $this->survey_model->get_survey_listing();
	    $surveySorted[$sections[$i]] =  $surveys;
	}

	// Mapping question and answer details
	foreach ($surveySorted as $key => $value) {
	    foreach ($value as $sectionwisevalue) {
		/*if($sectionwisevalue['s']['status'] == 0 || !isset($sectionwisevalue['s']['status']) || empty($sectionwisevalue['s']['status'])) {
		    $statusText = 'Not Saved';
		} else if($sectionwisevalue['s']['status'] == 1) {
		    $statusText = 'Saved';
		} else if ($sectionwisevalue['s']['status'] == 2) {
		    $statusText = 'Submitted';
		} else if($sectionwisevalue['s']['status'] == 3) {
		    $statusText = 'Updated';
		}*/
		$columns['question_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_text'];
		$columnsRow2['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['order_number'];
		$columnsRow3['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_options'];
		//$columnsRow4['question_option_'.$sectionwisevalue['s']['question_id']] = $statusText;
	    }
	}
	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
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
	//Row2 to display options
	$row = 2;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'H';
	$flag = 0;
	foreach ($columnsRow2 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow2[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	//Row3 to display identical numbers
	$row = 3;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'H';
	$flag = 0;
	foreach ($columnsRow3 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow3[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	$row = 4;
	$this->survey_model->year_id = date('Y') - 1;
	$dataArray = $this->survey_model->getExportArray();
	// Set audit log values for each survey
	foreach ($dataArray as $key => $value) {
	    $audit_detail = $this->survey_model->getLatestAuditSurveyDetailArchive($value['site_id']);
	    $dataArray[$key]['last_update_date'] = $audit_detail['last_update_date'];
	    $dataArray[$key]['last_update_by'] = $audit_detail['last_update_by'];
	}
	$siteWiseData = [];
	foreach ($dataArray as $key => $value) {
	    $siteWiseData[$value['site_id']]['attribute'] = $value['attribute'];
	    $siteWiseData[$value['site_id']]['property_name'] = $value['site_location_name'];
	    $siteWiseData[$value['site_id']]['country'] = $value['country'];
	    $siteWiseData[$value['site_id']]['region'] = $value['region_name'];
	    $siteWiseData[$value['site_id']]['property_type'] = $value['property_type'];
	    $siteWiseData[$value['site_id']]['last_update_by'] = $value['last_update_by'];
	    $siteWiseData[$value['site_id']]['last_update_date'] = $value['last_update_date'];
	    if(isset($value['question_id'])) {
		$siteWiseData[$value['site_id']]['question_'.$value['question_id']] = html_entity_decode(html_entity_decode(html_entity_decode(html_entity_decode($value['question_answer']))));
	    }
	    unset($value['site_id']);
	    unset($value['question_answer']);
	    unset($value['question_id']);
	}
	foreach ($siteWiseData as $data) {
	    foreach ($data as $key => $val) {
		if (array_key_exists($key, $cells)) {
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}

	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Last Year Survey Data.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }

    public function exportone()
    {
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Survey Data")
	    ->setKeywords("Survey Data");

	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'FFF2CC')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H1:LL1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'E2EFDA')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H2:LL2')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->getStyle('H3:LL3')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->setAutoFilter(
	    $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
	);
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$columns["attribute"] = 'Attribute';
	$columns["property_name"] = 'Property Name';
	$columns["country"] = 'Country';
	$columns["region"] = 'Region';
	$columns["property_type"] = 'Property Type';
	$columns["last_update_by"] = 'Last update by';
	$columns["last_update_date"] = 'Last update date';
	// Section wise sorted order question display
	$sections = explode('|', QUESTION_SECTIONS);
	$surveySorted = [];
	for($i=0; $i<=7; $i++) {
	    $this->survey_model->year_id = date('Y');
	    $this->survey_model->search_section = $sections[$i];
	    $surveys = $this->survey_model->get_survey_listing();
	    $surveySorted[$sections[$i]] =  $surveys;
	}

	// Mapping question and answer details
	foreach ($surveySorted as $key => $value) {
	    foreach ($value as $sectionwisevalue) {
		$columns['question_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_text'];
		$columnsRow2['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_options'];
		$columnsRow3['order_number_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['order_number'];
	    }
	}
	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
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
	//Row2 to display options
	$row = 2;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'H';
	$flag = 0;
	foreach ($columnsRow2 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow2[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	//Row3 to display Question label (ex: P-1)
	$row = 3;
	$cellsRow3 = array();
	$later1Row3 = "";
	$later2Row3 = 'H';
	$flag = 0;
	foreach ($columnsRow3 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row3 . $later2Row3 . $row, $column);
	    $cellsRow3[$key] = $later1Row3 . $later2Row3;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row3 . $later2Row3)->setWidth(15);
	    if ($later2Row3 == 'Z') {
		if ($flag == 0) {
		    $later1Row3 = 'A';
		    $flag = 1;
		} else {
		    $later1Row3++;
		}
		$later2Row3 = 'A';
	    } else {
		$later2Row3++;
	    }
	}
	$row = 4;
	$this->survey_model->year_id = date('Y');
	$dataArray = $this->survey_model->getExportOneArray();
	// Set audit log values for each survey
	foreach ($dataArray as $key => $value) {
	    $audit_detail = $this->survey_model->getLatestAuditSurveyDetail($value['site_id']);
	    $dataArray[$key]['last_update_date'] = (isset($audit_detail['last_update_date']) && strpos($audit_detail['last_update_date'], date('Y')) !== false) ? $audit_detail['last_update_date'] : '';
	    $dataArray[$key]['last_update_by'] = $audit_detail['last_update_by'];
	}
	$siteWiseData = [];
	foreach ($dataArray as $key => $value) {
	    $siteWiseData[$value['site_id']]['attribute'] = $value['attribute'];
	    $siteWiseData[$value['site_id']]['property_name'] = $value['site_location_name'];
	    $siteWiseData[$value['site_id']]['country'] = $value['country'];
	    $siteWiseData[$value['site_id']]['region'] = $value['region_name'];
	    $siteWiseData[$value['site_id']]['property_type'] = $value['property_type'];
	    $siteWiseData[$value['site_id']]['last_update_by'] = $value['last_update_by'];
	    $siteWiseData[$value['site_id']]['last_update_date'] = $value['last_update_date'];
	    if(isset($value['question_id'])) {
		$siteWiseData[$value['site_id']]['question_'.$value['question_id']] = html_entity_decode(html_entity_decode(html_entity_decode(html_entity_decode($value['question_answer']))));
	    }
	    unset($value['site_id']);
	    unset($value['question_answer']);
	    unset($value['question_id']);
	}
	foreach ($siteWiseData as $data) {
	    foreach ($data as $key => $val) {
		if (array_key_exists($key, $cells)) {
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}

	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Survey Data.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }

    public function exportarchieveone()
    {
	require_once APPPATH . 'libraries/PHPExcel/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("HEP")
	    ->setTitle("Survey Data")
	    ->setKeywords("Survey Data");

	$style = array('font' => array('bold' => true), 'align' => array(PHPExcel_Style_Alignment::HORIZONTAL_CENTER => true));
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($style);
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
	$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'FFF2CC')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H1:LL1')->applyFromArray(
	    array(
		'fill' => array(
		    'type' => PHPExcel_Style_Fill::FILL_SOLID,
		    'color' => array('rgb' => 'E2EFDA')
		)
	    )
	);
	$objPHPExcel->getActiveSheet()->getStyle('H2:LL2')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->getStyle('H3:LL3')->getFont()->setBold(true)
	    ->setSize(10)
	    ->getColor()->setRGB('A6A6A6');

	$objPHPExcel->getActiveSheet()->setAutoFilter(
	    $objPHPExcel->getActiveSheet()->calculateWorksheetDimension()
	);
	$site_id = isset($this->session->userdata[$this->section_name]['site_id']) ? $this->session->userdata[$this->section_name]['site_id'] : 0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail_custom($site_id);
	$columns["attribute"] = 'Attribute';
	$columns["property_name"] = 'Property Name';
	$columns["country"] = 'Country';
	$columns["region"] = 'Region';
	$columns["property_type"] = 'Property Type';
	$columns["last_update_by"] = 'Last update by';
	$columns["last_update_date"] = 'Last update date';
	// Section wise sorted order question display
	$sections = explode('|', QUESTION_SECTIONS);
	$surveySorted = [];
	for($i=0; $i<=7; $i++) {
	    $this->survey_model->year_id = date('Y') - 1;
	    $this->survey_model->search_section = $sections[$i];
	    $surveys = $this->survey_model->get_survey_listing();
	    $surveySorted[$sections[$i]] =  $surveys;
	}

	// Mapping question and answer details
	foreach ($surveySorted as $key => $value) {
	    foreach ($value as $sectionwisevalue) {
		$columns['question_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_text'];
		$columnsRow2['question_option_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['question_options'];
		$columnsRow3['order_number_'.$sectionwisevalue['s']['question_id']] = $sectionwisevalue['s']['order_number'];
	    }
	}
	$cells = array();
	$later1 = "";
	$later2 = 'A';
	$flag = 0;
	foreach ($columns as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1 . $later2 . "1", $column);
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
	//Row2 to display options
	$row = 2;
	$cellsRow2 = array();
	$later1Row2 = "";
	$later2Row2 = 'H';
	$flag = 0;
	foreach ($columnsRow2 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row2 . $later2Row2 . $row, $column);
	    $cellsRow2[$key] = $later1Row2 . $later2Row2;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row2 . $later2Row2)->setWidth(15);
	    if ($later2Row2 == 'Z') {
		if ($flag == 0) {
		    $later1Row2 = 'A';
		    $flag = 1;
		} else {
		    $later1Row2++;
		}
		$later2Row2 = 'A';
	    } else {
		$later2Row2++;
	    }
	}
	//Row3 to display Question label (ex: P-1)
	$row = 3;
	$cellsRow3 = array();
	$later1Row3 = "";
	$later2Row3 = 'H';
	$flag = 0;
	foreach ($columnsRow3 as $key => $column) {
	    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($later1Row3 . $later2Row3 . $row, $column);
	    $cellsRow3[$key] = $later1Row3 . $later2Row3;
	    $objPHPExcel->getActiveSheet()->getColumnDimension($later1Row3 . $later2Row3)->setWidth(15);
	    if ($later2Row3 == 'Z') {
		if ($flag == 0) {
		    $later1Row3 = 'A';
		    $flag = 1;
		} else {
		    $later1Row3++;
		}
		$later2Row3 = 'A';
	    } else {
		$later2Row3++;
	    }
	}
	$row = 4;
	$this->survey_model->year_id = date('Y') - 1;
	$dataArray = $this->survey_model->getExportOneArray();
	// Set audit log values for each survey
	foreach ($dataArray as $key => $value) {
	    $audit_detail = $this->survey_model->getLatestAuditSurveyDetailArchive($value['site_id']);
	    $dataArray[$key]['last_update_date'] = $audit_detail['last_update_date'];
	    $dataArray[$key]['last_update_by'] = $audit_detail['last_update_by'];
	}
	$siteWiseData = [];
	foreach ($dataArray as $key => $value) {
	    $siteWiseData[$value['site_id']]['attribute'] = $value['attribute'];
	    $siteWiseData[$value['site_id']]['property_name'] = $value['site_location_name'];
	    $siteWiseData[$value['site_id']]['country'] = $value['country'];
	    $siteWiseData[$value['site_id']]['region'] = $value['region_name'];
	    $siteWiseData[$value['site_id']]['property_type'] = $value['property_type'];
	    $siteWiseData[$value['site_id']]['last_update_by'] = $value['last_update_by'];
	    $siteWiseData[$value['site_id']]['last_update_date'] = $value['last_update_date'];
	    if(isset($value['question_id'])) {
		$siteWiseData[$value['site_id']]['question_'.$value['question_id']] = html_entity_decode(html_entity_decode(html_entity_decode(html_entity_decode($value['question_answer']))));
	    }
	    unset($value['site_id']);
	    unset($value['question_answer']);
	    unset($value['question_id']);
	}
	foreach ($siteWiseData as $data) {
	    foreach ($data as $key => $val) {
		if (array_key_exists($key, $cells)) {
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cells[$key] . $row, $val);
		}
	    }
	    $row++;
	}

	ob_end_clean();
	header('Content-Type: application//vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Last Year Survey Data.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
    }

    // GARIMA
    function saveAnswerLogic($postData,$buttonName) {
	$response = '';
	foreach ($postData as $key => $value) {
	    if (strpos($key, 'order_number_') !== false) {
		$stringArr = explode('_',$key);
		$question_id = $stringArr[3];
		$question = $this->survey_model->get_survey_question_detail_by_id($question_id);
		$question = $question[0];
		$order_number_id = displayLabelOnSurveyForm($question['section'], $stringArr[2]);
		$this->survey_model->updateOrderNumber($order_number_id, $question_id);
		$this->survey_model->updateSortOrderNumber($stringArr[2], $question_id);
	    } else {
		$value = $postData[$key];
		$question_id = substr($key, strrpos($key, '_') + 1);

		if(isset($question_id) && is_numeric($question_id)) {

		    $question_id = substr($key, strrpos($key, '_') + 1);
		    $question = $this->survey_model->get_survey_question_detail_by_id($question_id);
		    $question = $question[0];

		    $this->load->model('survey/survey_questions_answer_model');
		    $this->survey_questions_answer_model->question_id = $question_id;
		    $this->survey_questions_answer_model->year = date('Y');
		    $SurveyAnswer = $this->survey_questions_answer_model->get_survey_answer();
		    if(isset($question) && !empty($question)) {
			if(isset($value) && !empty($value)) {

			    if($question['question_type'] == 'checkbox') {

				$answer = implode('|', $value);

			    } else if ($question['question_type'] == 'multiselect') {

				$answers = [];
				$options = explode('|',$question['question_options']);
				foreach ($value as $selectedvalues) {
				    array_push($answers,$options[$selectedvalues]);
				}
				$answer = implode('|', $answers);
			    } else if ($question['question_type'] == 'dropdown') {
				$options = explode('|',$question['question_options']);

				$answer = $options[$value - 1];

			    } else {
				$answer = $value;
			    }

			    $this->load->model('survey/survey_questions_answer_model');
			    $this->survey_questions_answer_model->year = date('Y');
			    $this->survey_questions_answer_model->site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
			    if (isset($SurveyAnswer) && !empty($SurveyAnswer) && isset($SurveyAnswer['data'][0]) && isset($SurveyAnswer['data'][0]['s']['question_answer'])) {
				$this->survey_questions_answer_model->status = isset($SurveyAnswer['data'][0]['s']['status']) ? $SurveyAnswer['data'][0]['s']['status'] : 0;
			    }
			    $this->survey_questions_answer_model->question_id = $question_id;
			    $this->survey_questions_answer_model->question_answer = $answer;
			    if(isset($question_id) && !empty($question_id)) {
				$this->survey_questions_answer_model->insert_survey_questions_answer();
			    }
			    $prevAnswer = '';
			    if (isset($SurveyAnswer) && !empty($SurveyAnswer) && isset($SurveyAnswer['data'][0]) && isset($SurveyAnswer['data'][0]['s']['question_answer'])) {
				$prevAnswer = $SurveyAnswer['data'][0]['s']['question_answer'];
				$answerStatus = $SurveyAnswer['data'][0]['s']['status'];
			    }
			    $updateQuestionDataArray = [];
			    $updateQuestionDataArray['question'] = $question;
			    $updateQuestionDataArray['buttonName'] = $buttonName;
			    $updateQuestionDataArray['answer'] = $answer;
			    $updateQuestionDataArray['prevAnswer'] = $prevAnswer;
			    $updateQuestionDataArray['answerStatus'] = $answerStatus;
			    $updateQuestionDataArray['questionStatus'] = $question['status'];
			    if($response == '') {
				$response = $this->updateQuestionStatus($updateQuestionDataArray);
			    } else {
				$this->updateQuestionStatus($updateQuestionDataArray);
			    }
			} else {
			    $this->load->model('survey/survey_questions_answer_model');
			    $this->survey_questions_answer_model->question_id = $question_id;
			    $this->survey_questions_answer_model->delete_survey_questions_answer();
			}
		    }
		}
	    }
	}

	if(isset($_FILES) && !empty($_FILES)){
	    foreach ($_FILES as $file_key => $file_value) {
		$cpt = count($file_value['name']);
		    for($index=0; $index<$cpt; $index++)
		    {
			if(isset($file_value['name'][$index]) && !empty($file_value['name'][$index])) {
			    $question_id = substr($file_key, strrpos($file_key, '_') + 1);
			    if(is_numeric($question_id)) {
			    $imagename = $file_value['name'][$index];
			    $tempimagename = $file_value['tmp_name'][$index];
			    $size = $file_value['size'][$index];
			    $i = strrpos($imagename, ".");
			    if (!$i) {
				$ext = '';
			    }
			    $l = strlen($imagename) - $i;
			    $ext = substr($imagename, $i + 1, $l);
			    $survey_file_image_name = 'survey_file_' . rand(11111, 9999999) . '.' . $ext;
			    if ($ext) {
				// procedure further if and only if image size can not be more than 10MB.
				if ($size < (1024 * 1024 * 10)) {
				$uploadedfile = $tempimagename;
				    $target_file = BASE_PATH_CUSTOM . "/assets/uploads/" . $survey_file_image_name;
				    $_movestatus = move_uploaded_file($uploadedfile, $target_file);
				    if (!$_movestatus) {
					$this->theme->set_message('Todo image is not uploaded', 'error');
				    } else {
					$survey_image = trim(strip_tags($survey_file_image_name));
				    $answerUpload[$index] = $survey_image;
					}
				}else{
				    $this->theme->set_message('site image size is too large', 'error');
				    redirect(site_url() . BASE_ADMIN_URL_CUSTOM . 'survey/survey_form');
				    exit;
				}
			    }
			    }
			}
			$checkUploadKey = substr($file_key, 0, strrpos( $file_key,'_'));
			$this->load->model('survey/survey_questions_answer_model');
			$this->survey_questions_answer_model->year = date('Y');
			$this->survey_questions_answer_model->question_id = $question_id;
			if($checkUploadKey == 'survey_question_upload') {
			    $this->survey_questions_answer_model->question_upload = isset($answerUpload) && !empty($answerUpload) ? implode("|",$answerUpload) : '';
			} else {
			    $this->survey_questions_answer_model->question_answer = $answer;
			}
			$this->survey_questions_answer_model->status = 1;
			$this->survey_questions_answer_model->site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
			$this->survey_questions_answer_model->insert_survey_questions_answer();
		    }
	    }
	}
	$alertMessage = ($response != '')? $response : lang('msg_add_survey_success');
	$this->theme->set_message($alertMessage, 'success');
	if(isset($postData['nextTabPost']) && !empty($postData['nextTabPost'])) {
	    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/survey_form#survey-tabs'.$postData['nextTabPost']);
	} else {
	    redirect(BASE_ADMIN_URL_CUSTOM . 'survey/survey_form');
	}
    }

    function updateQuestionStatus($updateQuestionDataArray) {
	if($updateQuestionDataArray['buttonName'] == 'surveyFormSubmit') {
	    if($updateQuestionDataArray['answerStatus'] == 1 || $updateQuestionDataArray['answerStatus'] == 0) {
		$status = 2;
	    } else if($updateQuestionDataArray['answerStatus'] == 2){
		if($updateQuestionDataArray['answer'] != $updateQuestionDataArray['prevAnswer']) {
		    $status = 3;
		    $this->sentSurveyQuestionUpdateMailLog($updateQuestionDataArray['question'], $updateQuestionDataArray['prevAnswer'], $updateQuestionDataArray['answer']);
		    $response = "Survey answer for ".$updateQuestionDataArray['question']['order_number']." updated successfully.";
		}
	    }
	} else if($updateQuestionDataArray['buttonName'] == 'surveyFormSave') {
	    $status = 1;
	}
	if(isset($updateQuestionDataArray['question']['question_id']) && !empty($updateQuestionDataArray['question']['question_id'])) {
	    $this->load->model('survey/survey_questions_answer_model');
	    $this->survey_questions_answer_model->status = $status;
	    $this->survey_questions_answer_model->site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
	    $this->survey_questions_answer_model->question_id = $updateQuestionDataArray['question']['question_id'];
	    $this->survey_questions_answer_model->insert_survey_questions_answer();
	}
	if($status == 1) {
	    $response = "Survey saved successfully.";
	}
	if($status == 2) {
	    $user_id = isset($this->session->userdata[$this->section_name]['user_id'])?$this->session->userdata[$this->section_name]['user_id']:0;
	    $role_id = isset($this->session->userdata[$this->section_name]['role_id'])?$this->session->userdata[$this->section_name]['role_id']:0;
	    $site_id = isset($this->session->userdata[$this->section_name]['site_id'])?$this->session->userdata[$this->section_name]['site_id']:0;

	    $this->load->model('survey/survey_submit_log');
	    $this->survey_submit_log->user_id = $user_id;
	    $this->survey_submit_log->site_id = $site_id;
	    $submitLog = $this->survey_submit_log->get_survey_submit_log();
	    if(!isset($submitLog) && empty($submitLog)) {
		$this->sentSurveySubmissionMailLog($user_id, $site_id, $role_id);
		$response = "Survey submitted successfully.";
	    }
	}
	    return $response;
    }

    public function sentSurveySubmissionMailLog($user_id, $site_id, $role_id) {

	$this->load->library('mailer');
	$this->mailer->mail->IsHTML(true);

	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);

	$this->load->model('survey/survey_submit_log');
	$this->survey_submit_log->user_id = $user_id;
	$this->survey_submit_log->site_id = $site_id;
	$this->survey_submit_log->insert_survey_submit_log();

	$name = $this->session->userdata[$this->section_name]['firstname'].' '.$this->session->userdata[$this->section_name]['lastname'];
	$email = $this->session->userdata[$this->section_name]['email'];

	$subject = 'Survey Submission on ' . date('Y').'-'.date('m').'-'.date('d') . ' - ' . $site_detail['site_location_name'];

	$bodyHtml = '<div><h4>Dear ' .$name. '</h4></div>';
	// $bodyHtml .= '<div>This mail is regarding submission of survey by user <strong>' . $name . '</strong> for <strong>  '.SITE_APPLICATION_NAME.' - ' .  $site_detail['site_location_name'] . '.</strong></div>';
	$bodyHtml .= 'This mail is to confirm the submission of the ESG Survey by user ' . $name . '</strong> for <strong>  '.SITE_APPLICATION_NAME.' - ' .  $site_detail['site_location_name'] . '.</strong></div>';
	$email_template['html'] = $bodyHtml;

	$body = $this->load->view('email_template', $email_template, true);

	// $this->mailer->mail->AddAddress($user['email']);
	$this->mailer->mail->AddAddress($email);
	$this->mailer->mail->addBCC('rdiab@eegroup.info');
	// $this->mailer->mail->AddAddress('garima.pandey@tatvasoft.com');
	$this->mailer->mail->Subject = $subject;
	$this->mailer->mail->Body = $body;
	$this->mailer->mail->Send();
	$this->mailer->mail->ClearAllRecipients();

	return true;
    }

    public function sentSurveyQuestionUpdateMailLog($question, $prevAnswer, $answer) {

	$this->load->library('mailer');
	$this->mailer->mail->IsHTML(true);
	$user_id = isset($this->session->userdata[$this->section_name]['user_id'])?$this->session->userdata[$this->section_name]['user_id']:0;
	$role_id = isset($this->session->userdata[$this->section_name]['role_id'])?$this->session->userdata[$this->section_name]['role_id']:0;
	$site_id = isset($this->session->userdata[$this->section_name]['site_id'])?$this->session->userdata[$this->section_name]['site_id']:0;
	$this->load->model('sites/sites_model');
	$site_detail = $this->sites_model->get_site_detail($site_id, $user_id, $role_id);

	$name = $this->session->userdata[$this->section_name]['firstname'].' '.$this->session->userdata[$this->section_name]['lastname'];
	$email = $this->session->userdata[$this->section_name]['email'];

	$subject = 'Survey Updated on ' . date('Y').'-'.date('m').'-'.date('d') . ' - ' . $site_detail['site_location_name'];

	$bodyHtml = '<div><h4>Dear ' .$name. '</h4></div>';
	$bodyHtml .= 'This mail is regarding the update of the survey by user <strong>' . $name . '</strong> for <strong>  '.SITE_APPLICATION_NAME.' - ' .  $site_detail['site_location_name'] . '.</strong>. Answer of question '.$question['order_number'].' has been updated. For more information, please log to HEP <a href="'.base_url() . BASE_ADMIN_URL_CUSTOM .'survey/survey_form">Click here</a> to visit the survey page.';
	// $bodyHtml .= '<div>This mail is regarding update of survey by user <strong>' . $name . '</strong> for <strong>  '.SITE_APPLICATION_NAME.' - ' .  $site_detail['site_location_name'] . '.</strong>
	// Answer of question '.$question['order_number'].' is been updated from answer '.$prevAnswer.' to '.$answer.' <a href="'.base_url() . BASE_ADMIN_URL_CUSTOM .'survey/survey_form">Click here</a> to visit survey page.
	$bodyHtml .= '</div>';
	$email_template['html'] = $bodyHtml;

	$body = $this->load->view('email_template', $email_template, true);

	// $this->mailer->mail->AddAddress($user['email']);
	$this->mailer->mail->AddAddress($email);
	$this->mailer->mail->addBCC('rdiab@eegroup.info');
	// $this->mailer->mail->AddAddress('garima.pandey@tatvasoft.com');
	$this->mailer->mail->Subject = $subject;
	$this->mailer->mail->Body = $body;
	$this->mailer->mail->Send();
	$this->mailer->mail->ClearAllRecipients();

	return true;
    }
}