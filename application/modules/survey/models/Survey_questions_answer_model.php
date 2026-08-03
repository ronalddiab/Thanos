<?php



/**

 *  Survey_questions_answer Model

 *

 *  To perform queries related to user management.

 *

 * @package CIDemoApplication

 * @subpackage Survey

 * @copyright	(c) 2013, TatvaSoft

 * @author panks

 */

class Survey_questions_answer_model extends Base_Model

{

    protected $_table = TBL_SURVEY_QUESTIONS_ANSWER;

    public $year = '';



    /**

     * Function insert_survey_questions_answer to insert record

     */

    function insert_survey_questions_answer() {

        $data_array = array();

        $userId = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $siteId = isset($this->site_id) ? $this->site_id : $this->session->userdata[get_current_section($this, true)]['site_id'];

        if (isset($this->question_id)) {

            $data_array['question_id'] = $this->question_id;

        }

        if (isset($this->question_answer)) {

            $data_array['question_answer'] = $this->question_answer;

        } 

        if (isset($this->question_upload)) {
            $data_array['question_upload'] = $this->question_upload;
        }

        if (isset($siteId) && !empty($siteId)) {
            $data_array['site_id'] = $siteId;
        }

        $this->year = '';

        if(isset($data_array['question_id']) && !empty($data_array['question_id']) && (!empty($data_array['question_answer']) || !empty($data_array['question_upload']))){
            $response = $this->get_survey_answer();

        if($response['count'] > 0 && isset($response['data'])) {

            /*foreach ($response['data'] as $key => $value) {
            
                $isUpdate = substr($value['s']['created_on'], 0, 4) === date('Y');
            
                if ($isUpdate) {*/

                        $data_array['modify_by'] = $userId;
            
                        // $data_array['created_on'] = GetCurrentDateTime();
            
                        $data_action = 'Update';

                        $this->db->where(array('question_id' => $this->question_id, 'site_id' => $siteId));
            
                        $this->db->set($data_array);
            
                        $this->db->update($this->_table);
            
                        $id = $this->question_id;
            
                /*} else {
            
                        $data_array['created_on'] = GetCurrentDateTime();
            
                        $data_array['created_by'] = $userId;
            
                        $data_action = 'Create';         
                
            
        
                    $this->db->set($data_array);
        
                    $id = $this->db->insert($this->_table);            
        
                }
            }*/
        } else {

            $data_array['created_on'] = GetCurrentDateTime();
            
            $data_array['created_by'] = $userId;

            $data_action = 'Create';    

            $this->db->set($data_array);

            $id = $this->db->insert($this->_table);
        }



        // Save audit trail
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
        saveAuditTrail($userId, $site_id, 'Survey Questions Answer', $data_action);

            return $id; 
        } else {
            return false;
        }
    }

    public function delete_survey_questions_answer() {
        $userId = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $siteId = $this->session->userdata[get_current_section($this, true)]['site_id'];

        if (isset($this->question_id) && isset($siteId)) {
            $response = $this->get_survey_answer();

            if($response['count'] > 0) {

                $this->db->where('site_id', $siteId);
                $this->db->where('question_id', $this->question_id);
                $this->db->delete($this->_table);
                
                // Save audit trail
                $data_action = 'Delete Answer';
                saveAuditTrail($userId, $siteId, 'Survey Delete Answer -'.$this->question_id, $data_action);

                return true;
            }
        }
    }

    public function get_survey_answer()

    {

        $user_id = $this->session->userdata[get_current_section($this, true)]['user_id'];
        $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];

        $this->db->select('s.*');

        $this->db->where('s.site_id', $site_id);

        $this->db->where('s.deleted_at', null);

        $this->db->where('s.deleted_by', null);

        if(isset($this->question_id) && !empty($this->question_id) && $this->question_id != 0) {

            $this->db->where('s.question_id', $this->question_id);

        }

        if(isset($this->year) && !empty($this->year) && $this->year != 0) {

            $this->db->where("s.created_on LIKE '$this->year%'");

        }

        $this->db->from($this->_table . ' AS s');

        $query = $this->db->get();

        $answerExist = count($this->db->custom_result($query));

        $result = $this->db->custom_result($query);



        $response['count'] = $answerExist;

        $response['data'] = $result;



        return $response;

    }

    public function delete_survey_image($dataArray) {
        $this->db->select('s.*');
        $this->db->where('s.deleted_at', null);
        $this->db->where('s.deleted_by', null);

        if(isset($dataArray['question_id']) && !empty($dataArray['question_id']) && $dataArray['question_id'] != 0) {
            $this->db->where('s.question_id', $dataArray['question_id']);
        }
        if(isset($dataArray['survey_questions_answer_id']) && !empty($dataArray['survey_questions_answer_id']) && $dataArray['survey_questions_answer_id'] != 0) {
            $this->db->where('s.survey_questions_answer_id', $dataArray['survey_questions_answer_id']);
        }
        $this->db->from($this->_table . ' AS s');
        $result = $this->db->get()->row_array();

        if(isset($result) && !empty($result)) {
            $allFiles = explode("|", $result['question_upload']);
            $deleteIndex = array_search($dataArray['question_upload'], $allFiles);
            array_splice($allFiles, $deleteIndex, 1);
            $data_array['question_upload'] = isset($allFiles) && !empty($allFiles) ? implode("|", $allFiles) : NULL;
            $this->db->where(array('question_id' => $dataArray['question_id'], 'survey_questions_answer_id' => $dataArray['survey_questions_answer_id']));
            $this->db->set($data_array);
            $this->db->update($this->_table);
            
            // Save audit trail
            $data_action = 'Delete';
            $userId = $this->session->userdata[get_current_section($this, true)]['user_id'];
            $site_id = $this->session->userdata[get_current_section($this, true)]['site_id'];
            saveAuditTrail($userId, $site_id, 'Survey Delete Image', $data_action);

            return true;
        } else {
            return false;
        }
    }
}
