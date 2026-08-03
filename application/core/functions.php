<?php

/**
 *  Common function file
 *
 *  To perform define common functions and use it anywhere in system
 *
 * @package CIDemoApplication
 * @subpackage Common
 * @copyright	(c) 2013, TatvaSoft
 * @developer Pankit Shah <pankit.shah@sparsh.com>
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Function pr to print array without exit
 */
function pr($printarray) {
    echo "<pre>";
    print_r($printarray);
}

/**
 * Function pre to print array with exit
 */
function pre($printarray) {
    echo "<pre>";
    print_r($printarray);
    exit;
}

/**
 * Function num_format to print floating number in particular format for site
 */
function num_format($value, $decimal_places = 0, $isLocal=false) {
    $return = '';
    if (!empty($decimal_places)) {
        $value_decimal_count = strlen(substr(strrchr($value, "."), 1));
        if ($value < 0) {
            $value *= (-1);
            if(is_decimal($value)){
                if($value_decimal_count < $decimal_places){
                    $return = '- ' . currency_symbol($isLocal) . " " . number_format($value, $value_decimal_count);
                }else{
                    $return = '- ' . currency_symbol($isLocal) . " " . number_format($value, $decimal_places);
                }
            }else{
                $return = '- ' . currency_symbol($isLocal) . " " . number_format($value);
            }
            
        } else {
            
            if(is_decimal($value)){                
                if($value_decimal_count < $decimal_places){
                    $return = currency_symbol($isLocal) . ' ' . number_format($value, $value_decimal_count);
                } else {
                    $return = currency_symbol($isLocal) . ' ' . number_format($value, $decimal_places);
                }
                
            }else{
                $return = currency_symbol($isLocal) . ' ' . number_format($value);
            }
            
        }
    } else {
        if ($value < 0) {
            $value *= (-1);
            $return = '- ' . currency_symbol($isLocal) . " " . number_format($value);
        } else {
            $return = currency_symbol($isLocal) . ' ' . number_format($value);
        }
    }
    return $return;
}

/**
 * Function numb_format to print floating number in particular format for cron reports
 */
function numb_format($value, $decimal_places = 0, $isLocal=false) {
    $return = '';
    if (!empty($decimal_places)) {
        $value_decimal_count = strlen(substr(strrchr($value, "."), 1));
        if ($value < 0) {
            $value *= (-1);
            if(is_decimal($value)){
                if($value_decimal_count < $decimal_places){
                    $return = '- ' . cur_symbol($isLocal) . " " . number_format($value, $value_decimal_count);
                }else{
                    $return = '- ' . cur_symbol($isLocal) . " " . number_format($value, $decimal_places);
                }
            }else{
                $return = '- ' . cur_symbol($isLocal) . " " . number_format($value);
            }
            
        } else {
            
            if(is_decimal($value)){                
                if($value_decimal_count < $decimal_places){
                    $return = cur_symbol($isLocal) . ' ' . number_format($value, $value_decimal_count);
                } else {
                    $return = cur_symbol($isLocal) . ' ' . number_format($value, $decimal_places);
                }
                
            }else{
                $return = cur_symbol($isLocal) . ' ' . number_format($value);
            }
            
        }
    } else {
        if ($value < 0) {
            $value *= (-1);
            $return = '- ' . cur_symbol($isLocal) . " " . number_format($value);
        } else {
            $return = cur_symbol($isLocal) . ' ' . number_format($value);
        }
    }
    return $return;
}

/**
 * Function is_decimal to check weather no is decimal or not
 */
function is_decimal( $val )
{
    return is_numeric( $val ) && floor( $val ) != $val;
}

/**
 * Function get_section to get section name
 */
function get_section($obj, $front = false) {

    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }

    $controller_name = $obj->router->fetch_class();

    $parse_controller_name = explode("_", $controller_name);
    $parse_controller_name = end($parse_controller_name);

    if (isset($parse_controller_name) && $parse_controller_name == 'admin')
        return $parse_controller_name;
    elseif ($front == true)
        return 'front';
    else
        return '';
}

function get_current_section($obj, $front = false) {

    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }

    $controller_name = $obj->router->fetch_class();

    $parse_controller_name = end(explode("_", $controller_name));

    if (isset($parse_controller_name) && $parse_controller_name == 'admin') {
        return 'admin';
        //return $obj->uri->segment(1);
    }


    if ($obj->uri->segment(1) && isset($obj->section_name) && $obj->section_name == $obj->uri->segment(1)) {
        return $obj->uri->segment(1);
    } else {
        return 'admin';
    }
}

/**
 * Function get random string
 */
function get_random_string($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

/**
 * Function get_method to get method name
 */
function get_method($obj) {
    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }

    $method_name = $obj->router->fetch_method();

    if (isset($method_name) && $method_name)
        return $method_name;
    else
        return 'index';
}

/**
 * Function get_controller to get controller name
 */
function get_controller($obj) {
    return $obj->router->fetch_class();
}

/**
 * Function get_module to get module name
 */
function get_module($obj) {
    return $obj->router->fetch_module();
}

/**
 * Function for lock record when user edit any entry
 */
function lock_record($obj, $table, $id, $lock_user_id) {
    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }

    if ($lock_user_id == $obj->session->userdata[get_current_section($obj)]['user_id'] || $lock_user_id == 0) {
        $update_data = array(
            'is_locked' => 0,
            'lock_datetime' => date('Y:m:d H:i:s'),
            'lock_user_id' => 0
        );
        $obj->db->where('lock_user_id', $obj->session->userdata[get_current_section($obj)]['user_id']);
        $obj->db->update($table, $update_data);


        $update_data = array(
            'is_locked' => 1,
            'lock_datetime' => date('Y:m:d H:i:s'),
            'lock_user_id' => $obj->session->userdata[get_current_section($obj)]['user_id']
        );
        $obj->db->where('id', $id);
        $obj->db->update($table, $update_data);
        return true;
    } else {
        return false;
    }
}

function unlock_record($obj, $table, $id) {
    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }
    $update_data = array(
        'is_locked' => 0,
        'lock_datetime' => date('Y:m:d H:i:s'),
        'lock_user_id' => 0
    );
    $obj->db->where('id', $id);
    $obj->db->update($table, $update_data);
}

function unlock_all($obj, $table) {
    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }
    $result = $obj->db->query("SELECT id,time_to_sec(TIMEDIFF('" . date('Y-m-d H:i:s') . "',lock_datetime))/60 as time_cal from " . $table . " where is_locked=1 and lock_user_id<>0  ");

    $data_result = $result->result_array();

    if (!empty($data_result)) {
        foreach ($data_result as $key => $value) {

            if (intval($value['time_cal']) > 5) {
                $update_data = array(
                    'is_locked' => 0,
                    'lock_datetime' => date('Y:m:d H:i:s'),
                    'lock_user_id' => 0
                );
                $obj->db->where('id', $value['id']);
                $obj->db->update($table, $update_data);
            }
        }
    }
}

function get_template_body($obj, $template_name, $mail_var, $lang_id) {
    $default_language = $obj->languages_model->get_default_language();

    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }
    $result = $obj->db->query("SELECT * from " . TBL_EMAIL_TEMPLATE . " where lang_id=" . $lang_id . " and template_name='" . $template_name . "'  and status =1 ");
    $data_result = $result->result_array();

    if (!empty($data_result)) {
        $body = srepltags_array($mail_var, $data_result[0]['template_body']);
    } else {
        $lang_id = $default_language[0]['l']['id'];
        $result = $obj->db->query("SELECT * from " . TBL_EMAIL_TEMPLATE . " where lang_id=" . $lang_id . " and template_name='" . $template_name . "'  and status =1 ");
        $default_result = $result->result_array();

        if (!empty($default_result)) {
            $body = srepltags_array($mail_var, $default_result[0]['template_body']);
        } else {
            $body = " ";
        }
    }


    return $body;
}

function get_template_subject($obj, $template_name) {
    $default_language = $obj->languages_model->get_default_language();

    if (isset($obj->_ci)) {
        $obj = $obj->_ci;
    }
    $result = $obj->db->query("SELECT * from " . TBL_EMAIL_TEMPLATE . " where lang_id=" . $obj->session->userdata[get_current_section($obj)]['site_lang_id'] . " and template_name='" . $template_name . "'  and status =1 ");
    $data_result = $result->result_array();
    return $data_result[0]['template_subject'];
}

function srepltags_array($arr, $str) {
    if (is_array($arr)) {
        reset($arr);
        $keys = array_keys($arr);
        array_walk($keys, create_function('&$val', '$val = "[$val]";'));
        $vals = array_values($arr);
        //return ereg_replace( "[([0-9A-Za-z\_\s\-]+)]", "", str_replace( $keys, $vals, $str));
        return preg_replace('/^[0-9a-zA-Z\/_\/s\/-]+/', '', str_replace($keys, $vals, $str));
        ;
    } else {
        return $str;
    }
}

function get_languages() {
    require_once( BASEPATH . 'database/DB' . EXT );
    $db = & DB();

//    $query = $db->query("SELECT GROUP_CONCAT( language_code separator '|') as lang_code FROM  `languages`");
    // $row = $query->row();

    $db->select("GROUP_CONCAT(l.language_code separator '|') as lang_code");
    $db->from('languages l');
    $db->where("l.status", 1);
    $query = $db->get();
    //echo $db->last_query();exit;
    $row = $query->row();
    return $row->lang_code;
}

function clean($string) {

    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return $string; // Removes special chars.
}



function trim_formatted_trailing_zeros($formatted) {

    if (strpos($formatted, '.') !== false) {

        $formatted = rtrim($formatted, '0');

        $formatted = rtrim($formatted, '.');

    }

    return $formatted;

}



function num_format_without_currency($value, $decimal_places = 0, $isLocal=false) {
    $return = '';
    if (!empty($decimal_places)) {

        $value_decimal_count = strlen(substr(strrchr((string) $value, "."), 1));

        if ($value < 0) {
            $value *= (-1);
            if(is_decimal($value)){
                if($value_decimal_count < $decimal_places){
                    $return = '- ' . number_format($value, $value_decimal_count);
                }else{
                    $return = '- ' . number_format($value, $decimal_places);
                }
            }else{
                $return = '- ' . number_format($value);
            }
            
        } else {
            
            if(is_decimal($value)){                
                if($value_decimal_count < $decimal_places){
                    $return = number_format($value, $value_decimal_count);
                } else {
                    $return = number_format($value, $decimal_places);
                }
                
            }else{
                $return = number_format($value);
            }
            
        }
    } else {
        if ($value < 0) {
            $value *= (-1);
            $return = '- ' . number_format($value);
        } else {
            $return = number_format($value);
        }
    }

    return trim_formatted_trailing_zeros($return);

}

function numb_format_without_currency($value, $decimal_places = 0, $isLocal=false) {
    $return = '';
    if (!empty($decimal_places)) {

        $value_decimal_count = strlen(substr(strrchr((string) $value, "."), 1));

        if ($value < 0) {
            $value *= (-1);
            if(is_decimal($value)){
                if($value_decimal_count < $decimal_places){
                    $return = '- ' . number_format($value, $value_decimal_count);
                }else{
                    $return = '- ' . number_format($value, $decimal_places);
                }
            }else{
                $return = '- ' . number_format($value);
            }
            
        } else {
            
            if(is_decimal($value)){                
                if($value_decimal_count < $decimal_places){
                    $return = number_format($value, $value_decimal_count);
                } else {
                    $return = number_format($value, $decimal_places);
                }
                
            }else{
                $return = number_format($value);
            }
            
        }
    } else {
        if ($value < 0) {
            $value *= (-1);
            $return = '- ' . number_format($value);
        } else {
            $return = number_format($value);
        }
    }

    return trim_formatted_trailing_zeros($return);

}
