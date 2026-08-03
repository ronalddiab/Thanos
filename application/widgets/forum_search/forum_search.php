<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class forum_search extends widgets {

    public function __construct() {
        parent::__construct();
    }

    function run() {
        $data = array();
        $CI = & get_instance();


        $industry_list = array();//$this->sector_model->get_all_industry();
        $industry = "";


        $sector = "";
        $sector_list_result = array();//$this->sector_model->get_record_listing();
        $sector_list[''] = '---All Sectors---';
        foreach ($sector_list_result as $result) {
            $sector_list[$result['R']['sector_id']] = $result['R']['sector_name'];
        }

        $data['ci'] = $CI;
        $data['industry_list'] = $industry_list;
        $data['industry'] = $industry;
        $data['sector_list'] = $sector_list;
        $data['sector'] = $sector;

        return $this->build('forum_search_view', $data);
    }

}
