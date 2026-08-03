<?php



/**

 *  Survey Model

 *

 *  To perform queries related to user management.

 *

 * @package CIDemoApplication

 * @subpackage Survey

 * @copyright	(c) 2013, TatvaSoft

 * @author panks

 */

class Survey_model extends Base_Model

{

    protected $_table = TBL_SURVEY_QUESTION;

    protected $_tbl_survey_questions_answer = TBL_SURVEY_QUESTIONS_ANSWER;

    public $search_term = "";
    public $search_section = "";
    public $search_source = "";
    public $year_id = '';

    public $group_by = false;

    public $_record_count;

    

    public function get_survey_listing()

    {

        if ($this->search_term != "") {

            $this->db->like("LOWER(s.question_text)", strtolower($this->search_term));

        }

        if ($this->search_section != "") {
            
            $this->db->like("LOWER(s.section)", strtolower($this->search_section));

        }

        if ($this->search_source != "") {

            $this->db->like("LOWER(s.source)", strtolower($this->search_source));

        }
        if ($this->group_by) {

            // $this->db->group_by('s.source');
        }
        if ($this->year_id) {
            $this->db->where('s.year_id', $this->year_id);
        }



        $this->db->select('s.*');

        $this->db->where('s.deleted_at', null);
        
        $this->db->where('s.deleted_by', null);
        
        if (($this->search_section != "" || $this->search_section == 0) && $this->search_term == "" && $this->search_source == "") {
            $this->db->order_by('sort_order_number', 'ASC');
        } else {
            $this->db->order_by('question_id', 'ASC');
            if (isset($this->record_per_page) && isset($this->offset) && !isset($this->_record_count) && $this->_record_count != true) {
                $this->db->limit($this->record_per_page, $this->offset);
            }
        }
        $this->db->where('s.question_text is NOT NULL', NULL, FALSE);
        $this->db->where('s.question_text != ""', NULL, FALSE);
        $this->db->from($this->_table . ' AS s');

        $query = $this->db->get();

        if (isset($this->_record_count) && $this->_record_count == true) {

            return count($this->db->custom_result($query));

        } else {

            return $this->db->custom_result($query);

        }

    }



    /**

     * Function insert_survey_question to insert record

     */

    function insert_survey_question($survey_question_id) {

        $survey_question_id = intval($survey_question_id);
        $data_array = $questionDetail = array();

        if($survey_question_id != 0) {
            $data_array['question_id'] = $survey_question_id;
            $questionDetail = $this->get_survey_question_detail_by_id($survey_question_id);
            $questionDetail = $questionDetail[0];
        }

        if (isset($this->question_text)) {
            $data_array['question_text'] = $this->question_text;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->question_text)) {
            $data_array['question_text'] = $questionDetail['question_text'];
        }

        if (isset($this->question_type)) {
            $data_array['question_type'] = $this->question_type;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->question_type)) {
            $data_array['question_type'] = $questionDetail['question_type'];
        }

        if (isset($this->question_options)) {
            $data_array['question_options'] = $this->question_options;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->question_options)) {
            $data_array['question_options'] = $questionDetail['question_options'];
        }

        if (isset($this->required)) {
            $data_array['required'] = $this->required ? $this->required : 'No';
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->required)) {
            $data_array['required'] = $questionDetail['required'];
        }

        if (isset($this->source)) {
            $data_array['source'] = $this->source;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->source)) {
            $data_array['source'] = $questionDetail['source'];
        }

        if (isset($this->question_description)) {
            $data_array['question_description'] = $this->question_description;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->question_description)) {
            $data_array['question_description'] = $questionDetail['question_description'];
        }

        if (isset($this->section)) {
            $data_array['section'] = $this->section;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->section)) {
            $data_array['section'] = $questionDetail['section'];
        }

        if (isset($this->is_upload)) {
            $data_array['is_upload'] = $this->is_upload;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->is_upload)) {
            $data_array['is_upload'] = $questionDetail['is_upload'];
        } else {
            $data_array['is_upload'] = 0;
        } 

        if (isset($this->year_id)) {
            $data_array['year_id'] = (int)$this->year_id;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->year_id)) {
            $data_array['year_id'] = $questionDetail['year_id'];
        }  

        if (isset($this->status)) {
            $data_array['status'] = (int)$this->status;
        } else if(isset($questionDetail) && !empty($questionDetail) && $survey_question_id != 0 && empty($this->status)) {
            $data_array['status'] = $questionDetail['status'];
        } 

        if($survey_question_id == 0) {

            $data_array['created_on'] = GetCurrentDateTime();

            $data_array['created_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];

            $data_action = 'Create';


            if(isset($data_array['question_text']) && !empty(isset($data_array['question_text']))){

                $this->db->set($data_array);

                $id = $this->db->insert($this->_table);
            } else {
                $id = 0;
            }

        } else {

            $data_array['modify_on'] = GetCurrentDateTime();

            $data_array['modify_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];

            $data_action = 'Update';


            $this->db->where(array('question_id' => $survey_question_id));

            $this->db->set($data_array);

            $this->db->update($this->_table);

            $id = $survey_question_id;

        }



        // Save audit trail
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];

        saveAuditTrail($user_id, $site_id, 'Survey Question', $data_action);



        return $id; 

    }

	public function get_question_id($order_number)
	{
		$this->db->select('*')
			->from($this->_table)
			->where('order_number', $order_number);
		$query = $this->db->get();
		return $query->result_array();
	}

    public function get_survey_question_detail_by_id($survey_question_id)

    {

        $survey_question_id = intval($survey_question_id);

        $this->db->select('*')

                ->from($this->_table)

                ->where(array('question_id' => $survey_question_id));

        $query = $this->db->get();

        return $query->result_array();

    }



    /**

     * Function delete_survey to delete survey

     * @param integer $id

     */

    public function delete_survey($id)

    {

        //Type Casting

        $id = intval($id);        

        $data_array['deleted_at'] = GetCurrentDateTime();

        $data_array['deleted_by'] = $this->session->userdata[get_current_section($this, true)]['user_id'];



        $this->db->where(array('question_id' => $id));

        $this->db->set($data_array);

        return $this->db->update($this->_table);

    }

    public function updateOrderNumber($order_label, $id)
    {
        $data_array['order_number'] = $order_label;
        $this->db->where(array('question_id' => $id));
        $this->db->set($data_array);
        return $this->db->update($this->_table);
    }

    public function updateSortOrderNumber($sort_order_number, $id)
    {
        $data_array['sort_order_number'] = $sort_order_number;
        $this->db->where(array('question_id' => $id));
        $this->db->set($data_array);
        return $this->db->update($this->_table);
    }

    public function getExportArray()
    {
        $query = "SELECT 
                `sites`.`attribute`,
                `sites`.`site_location_name`,
                `countries`.`country`,
                `regions`.`region_name`,
                    case `sites`.`site_type`
                        when 1 then 'Resort'
                        when 2 then 'City Hotel'
                        when 3 then 'Residences'
                        when 4 then 'Corporate Office'
                    end as property_type,
                `question_answer`,
                `survey_questions_answer`.`question_id`,
                `survey_questions_answer`.`site_id`
                FROM `survey_questions_answer` 
                LEFT JOIN `sites` ON sites.id = survey_questions_answer.site_id
                LEFT JOIN `countries` ON countries.id = sites.country_id
                LEFT JOIN `regions` ON regions.id = sites.region_id
                LEFT JOIN `survey_question` ON survey_question.question_id = survey_questions_answer.question_id
                WHERE 
                `sites`.`status` != '-1' AND 
                `sites`.`id`  NOT IN (2,12,64) AND 
                `survey_questions_answer`.`deleted_at` IS NULL AND 
                `survey_questions_answer`.`deleted_by` IS NULL AND
                `survey_questions_answer`.`site_id` IS NOT NULL AND
                `survey_question`.`year_id` = ".$this->year_id."
                ";           
                        
        $result = $this->db->query($query);
        $result = $result->result_array();

        $query2 ="SELECT 
                `sites`.`id` as `site_id`,
                `sites`.`attribute`,
                `sites`.`site_location_name`,
                `countries`.`country`,
                `regions`.`region_name`,
                    case `sites`.`site_type`
                        when 1 then 'Resort'
                        when 2 then 'City Hotel'
                        when 3 then 'Residences'
                        when 4 then 'Corporate Office'
                    end as property_type
                FROM sites 
                LEFT JOIN `survey_questions_answer` ON survey_questions_answer.site_id = sites.id 
                LEFT JOIN `countries` ON countries.id = sites.country_id
                LEFT JOIN `regions` ON regions.id = sites.region_id
                LEFT JOIN `survey_question` ON survey_question.question_id = survey_questions_answer.question_id
                WHERE survey_questions_answer.site_id IS NULL
                AND `sites`.`status` != '-1' 
                AND `sites`.`id`  NOT IN (2,12,64)
                AND sites.id IS NOT NULL
                AND `survey_question`.`year_id` = ".$this->year_id."";
            $result2 = $this->db->query($query2);
            $result2 = $result2->result_array();

        $surveys = $this->db->select('distinct(site_id)')
			->from('survey_questions_answer')
            ->where('site_id <>',NULL)->get();
        $surveys = $surveys->result_array();
        $surveyIds = $sitesArray = [];
        foreach ($surveys as $value) {
            $surveyIds[] = $value['site_id'];
        }
        $sites = $this->db->select('id')
			->from('sites')
			->where('status <>', -1)
            ->where_not_in('id', $surveyIds)
            ->get();
        $sites = $sites->result_array();
        foreach ($sites as $key => $value) {
            $sitesArray[] = $value['id'];
        }
        $query3 ="SELECT 
        `sites`.`id` as `site_id`,
        `sites`.`attribute`,
        `sites`.`site_location_name`,
        `countries`.`country`,
        `regions`.`region_name`,
            case `sites`.`site_type`
                when 1 then 'Resort'
                when 2 then 'City Hotel'
                when 3 then 'Residences'
                when 4 then 'Corporate Office'
            end as property_type
        FROM sites 
        LEFT JOIN `countries` ON countries.id = sites.country_id
        LEFT JOIN `regions` ON regions.id = sites.region_id
        WHERE `sites`.`status` != '-1' 
        AND `sites`.`id`  NOT IN (2,12,64)
        AND sites.id IS NOT NULL";
        $result3 = $this->db->query($query3);
        $result3 = $result3->result_array();
        return array_merge($result,$result2,$result3);
    }

    public function getLatestAuditSurveyDetail($site_id)
    {
        $query = "SELECT 
            `users`.`username` as last_update_by,
            DATE_FORMAT(`audit_trail`.`created`, '%Y-%m-%d') as last_update_date
            FROM audit_trail 
            LEFT JOIN `users` ON users.id = audit_trail.user_id
            WHERE audit_trail.module_name LIKE '%Survey Question%' AND audit_trail.site_id =".$site_id." 
            ORDER BY audit_trail.id DESC 
            LIMIT 1";
        $result = $this->db->query($query);
        return $result->row_array();
    }

    public function getExportOneArray()
    {
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
        $query = "SELECT 
                `sites`.`attribute`,
                `sites`.`site_location_name`,
                `countries`.`country`,
                `regions`.`region_name`,
                    case `sites`.`site_type`
                        when 1 then 'Resort'
                        when 2 then 'City Hotel'
                        when 3 then 'Residences'
                        when 4 then 'Corporate Office'
                    end as property_type,
                `users`.`username`,
                DATE_FORMAT(`audit_trail`.`created`, '%Y-%m-%d') as updated_date,
                `question_answer`,
                `survey_questions_answer`.`question_id`,
                `survey_questions_answer`.`site_id`
                FROM `survey_questions_answer` 
                LEFT JOIN `sites` ON sites.id = survey_questions_answer.site_id
                LEFT JOIN `audit_trail` ON audit_trail.id = survey_questions_answer.site_id
                LEFT JOIN `users` ON users.id = audit_trail.user_id
                LEFT JOIN `countries` ON countries.id = sites.country_id
                LEFT JOIN `regions` ON regions.id = sites.region_id
                LEFT JOIN `survey_question` ON survey_question.question_id = survey_questions_answer.question_id
                WHERE 
                `survey_questions_answer`.`deleted_at` IS NULL AND 
                `survey_questions_answer`.`deleted_by` IS NULL AND
                `survey_questions_answer`.`site_id` IS NOT NULL AND
                `survey_questions_answer`.`site_id` = {$site_id} AND
                `survey_question`.`year_id` = ".$this->year_id."
                ";           
                        
        $result = $this->db->query($query);
        return $result->result_array();
    }
}
