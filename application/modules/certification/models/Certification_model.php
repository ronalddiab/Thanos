<?php

/**
 *  certification_model Model
 *
 *  To perform queries related to certification management.
 *
 * @package CIDemoApplication
 * @subpackage Users
 * @copyright	(c) 2013, TatvaSoft
 * @author panks
 */
class certification_model extends Base_Model {
    protected $_tbl_menus = TBL_MENU_NAVIGATION;
    protected $_tbl_question_answers = TBL_QUESTION_ANSWERS;
    protected $_tbl_questions = TBL_SECTION_QUESTIONS;

    function get_section_info_by_slug($slug='')
    {
        $this->db->where("c.slug",$slug);
        $this->db->from($this->_tbl_menus . ' as c');
        $this->db->join($this->_tbl_menus . ' as main_menu','main_menu.id = c.main_menu_id','left');
        $this->db->where_in("c.status", array(1, 0));
        $this->db->select('c.*,main_menu.title as main_menu_title');
        $tableMenu = $this->db->get($this->_tbl_menus);
       
        $menuArray = $tableMenu->row_array();
        if (!empty($menuArray)) {
            return $menuArray;
        } else {
            return [];
        }
    }
    function save_ans($data)
    {
        
        if(isset($data['ans']) && count($data['ans'])>0)
        {
            $ans_data = [];
            foreach($data['ans'] as $key=>$ans)
            {
                $ans_data['user_id'] = $data['user_id'];
                $ans_data['section_question_id'] = $key;
                $ans_data['section_question_comments'] = $ans['comments'];
                $ans_data['section_question_answer'] = isset($ans['section_question_answer'])?$ans['section_question_answer']:'N';
                $ans_data['created_at'] = date('Y-m-d H:i:s');
                if(isset($ans['answer_id']) && $ans['answer_id']!='')
                {
                    $this->db->where('id', $ans['answer_id']);
                    $this->db->where('user_id', $data['user_id']);
                    $this->db->update($this->_tbl_question_answers, $ans_data);
                    $id = $ans_data['id'];
                }
                else{
                    if ($this->db->insert($this->_tbl_question_answers, $ans_data)) {
                        $id = $this->db->insert_id();
                    }
                }
            }
        }          
    }
    function get_question_answer($que_id,$user_id)
    {
        $this->db->where("section_question_id",$que_id);
        $this->db->where("user_id",$user_id);
        $table_ans = $this->db->get($this->_tbl_question_answers);
        $ansArray = $table_ans->row_array();
        if (!empty($ansArray)) {
            return $ansArray;
        } else {
            return [];
        }
    }
}