<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');
/* The MX_Controller class is autoloaded as required */

class MY_Form_validation extends CI_Form_validation {

    public function __construct() {
        parent::__construct();
    }

    function run($module = '', $group = '') {
        (is_object($module)) AND $this->CI = &$module;
        return parent::run($group);
    }

    public function is_unique($str, $field) {

        $field_ar = explode('.', $field);
        $condition = array();
        $condition["lower(".$field_ar[1].")"] = strtolower($str);
        if (isset($field_ar[2]) && isset($field_ar[3]) && $field_ar[2] != '' && $field_ar[3] != '')
            $condition[$field_ar[2] . " !="] = $field_ar[3];

        $query = $this->CI->db->get_where($field_ar[0], $condition, 1, 0);

        if ($query->num_rows() === 0) {
            return TRUE;
        }
        return FALSE;
    }

    //Check URL exits via Curl request of Codeigniter
    public function url_exist($str, $field) {
        $field_ar = explode('.', $field);
        $url = $field_ar[1].'/'.$str;

	$CI = & get_instance();
        $result =  $CI->curl->simple_get($url);

	if ($CI->curl->error_code != 0) {
            $this->set_message('url_exist', 'The URL you have entered is Invalid');
            return false;
        } else {
            return true;
        }
    }

    public function urlexist($str, $field) {

        $CI = & get_instance();
        $result =  $CI->curl->simple_get($str);
        if ($CI->curl->error_code != 0) {
            $this->set_message('urlexist', 'The URL you have entered is Invalid');
            return false;
        } else {
            return true;
        }
    }

    function validate_captcha($word) {

        $CI = & get_instance();

        if (empty($word) || $word != $CI->session->userdata['word']) {
            $CI->form_validation->set_message('validate_captcha', 'The letters you entered do not match the image.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    //Check URL exits via Curl request of Codeigniter
    public function child_permission($str, $field) {
        $this->set_message('child_permission', 'Parent permission can not be assigned to child/itself');
        return false;
    }

    public function child_menu($str, $field) {
        $this->set_message('child_menu', 'Parent menu can not be assigned to child menu');
        return false;
    }

    function checkdefault($str,$field){
        $this->set_message('checkdefault', lang('languages-message-default'));
        return false;
    }

}