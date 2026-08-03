<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  Base Front Controller
 *
 *  Base Front controller to set settings for site's front section.
 *
 * @package CIDemoApplication
 *
 * @copyright	(c) 2013, TatvaSoft
 * @author Amit Patel <amit.patel@sparsh.com>
 */
class Base_Front_Controller extends Base_Controller {

    public function __construct() {
        parent::__construct();

        $this->section_name = 'admin';
        //$this->section_name = "admin";
        //$this->theme->set_theme("admin");
        $this->theme->set_theme("default");
        $this->theme->set('section_name', $this->section_name);
        $this->load->library("my_pagination");
        $this->load->plugin('pagination');
        //language setting for front end
        if (!isset($this->session->userdata[$this->section_name]['site_lang_id']) || $this->session->userdata[$this->section_name]['site_lang_id'] == '') {
            $default_language = $this->languages_model->get_default_language();
            $this->site_language($default_language[0]['l']['id'], $default_language[0]['l']['language_name'], $default_language[0]['l']['language_code'], $default_language[0]['l']['direction']);
        }
        $all_lang = $this->languages_model->getalllanguages();

        if ($this->input->post('select_language') != '') {

            /*
             * @OLD Code
             */

            $language_detail = $this->languages_model->get_languages($this->input->post('select_language'));
            $this->site_language($language_detail[0]['l']['id'], $language_detail[0]['l']['language_name'], $language_detail[0]['l']['language_code'], $language_detail[0]['l']['direction']);
            redirect('/');
            /*

             * @OLD Code
             */


            /*
             * @New Code
             */

//            $language_detail = $this->languages_model->get_languages($this->input->post('select_language'));
//
//            if (isset($this->uri->segments[1]) && $this->uri->segments[1] != "" && array_key_exists($this->uri->segments[1], $all_lang)) {
//
//                $uri = $this->lang->switch_uri($language_detail[0]['l']['language_code']);
//
//
//                // Get all slugs for CMS
//                $slugUrlStr = $this->getslugUrlStr();
//                $slugUrlArr = explode(',', $slugUrlStr);
//
//                if (in_array($this->uri->segments[2], $slugUrlArr)) { // Check for CMS Page
//                    $getCurrentCMSSlugURL = $this->getCurrentCMSSlugURL($this->uri->segments[2], $language_detail[0]['l']['id']);
//                    $uri = $language_detail[0]['l']['language_code'] . '/' . $getCurrentCMSSlugURL;
//                    redirect(site_base_url() . $uri);
//                } else { // Not CMS then redirect to same page with lang code.
//                    redirect(site_base_url() . $uri);
//                } //  Check for CMS Page  --X-X-X-X-X-X-X-X--
//            } else {
//                redirect('/');
//            }

            /*
             * @New Code
             */


        } else {
            if (isset($this->uri->segments[1]) && array_key_exists($this->uri->segments[1], $all_lang)) {
                //echo array_key_exists($this->uri->segments[1],$all_lang);exit;
                $language_detail = $this->languages_model->get_languages_by_code($this->uri->segments[1]);
                $this->site_language($language_detail[0]['l']['id'], $language_detail[0]['l']['language_name'], $language_detail[0]['l']['language_code'], $language_detail[0]['l']['direction']);
            }
        }


        if (file_exists(APPPATH . "modules/" . $this->_module . "/models/" . $this->_module . "_model" . EXT) || file_exists(APPPATH . "models/" . $this->_module . "_model" . EXT)) {
            $this->{$this->_module . '_model'}->language_id = $this->session->userdata[$this->section_name]['site_lang_id'];
        }

        $this->lang->load('application', $this->session->userdata[$this->section_name]['site_lang_name']);
        $this->lang->load($this->_module, $this->session->userdata[$this->section_name]['site_lang_name']);

        if (ACTIVITY_LOG == 1) {
            $this->activity_log();
        }
        if (!isset($this->session->userdata[$this->section_name]["record_per_page"]))
            $this->session->set_custom_userdata($this->section_name, "record_per_page", RECORD_PER_PAGE);
        $this->set_custom_pagination_code($this->section_name);
    }

    /**
     * Function is_front_login to check user logged in to the front site or not
     */
    protected function is_front_login() {
        if (isset($this->session->userdata[$this->section_name]['front_user_id'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function site_language to set all language related variables in session
     */
    protected function site_language($language_id, $language_name, $language_code, $direction) {
        if (!isset($language_id) || $language_id == '') {
            show_error(lang('no-language-defind'));
        } else {
            $this->set_ci_session($this->section_name, 'site_lang_id', $language_id);
            $this->set_ci_session($this->section_name, 'site_lang_name', strtolower($language_name));
            $this->set_ci_session($this->section_name, 'site_lang_code', ($language_code));
            $this->set_ci_session($this->section_name, 'site_direction', ($direction));
        }
    }

    /**
     * Function to check permission of page by role
     */
    protected function check_permission() {
        $this->load->model('roles/roles_model');
        $this->load->model('permissions/permissions_model');

        if (isset($this->uri->rsegments[2]) && $this->uri->rsegments[2] == 'action') {
            $uri = $this->section_name . '.' . $this->uri->segments[1] . '.' . $this->uri->rsegments[2] . '.' . $this->uri->rsegments[3];
        } else {
            $uri = $this->section_name . '.' . $this->uri->segments[1] . '.' . $this->uri->rsegments[2];
        }
        if (isset($this->session->userdata[$this->section_name]['super_user']) && $this->session->userdata[$this->section_name]['super_user'] == '1') {
            return true;
        }
        $result = $this->permissions_model->get_permission_id_by_name($uri);
        if (!empty($result) && isset($this->session->userdata[$this->section_name]['role_id']) && $this->session->userdata[$this->section_name]['role_id'] != '') {
            $role_id = (isset($this->session->userdata[$this->section_name]['role_id']) ? $this->session->userdata[$this->section_name]['role_id'] : '1');
            $permission_id = $result[0]['id'];
            if ($this->has_permission($role_id, $permission_id)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Function to has_permission to check user have permission or not
     */
    protected function has_permission($role_id = NULL, $permission_id = NULL) {
        $this->load->model('roles/roles_model');

        $permission = $this->roles_model->check_permission($role_id, $permission_id);

        if (empty($permission)) {
            return false;       //doesn't have permission
        } else {
            return true;        //has permission
        }
    }




    /*
     *  @Language Change Fn
     */

    protected function getCurrentCMSSlugURL($slug, $lang_id) {


        // General rule for getting Slug URL

        $sql2 = "SELECT table_name, field_name FROM ".TBL_URL_MANAGEMENT." WHERE slug_url = '".$slug."' ";
        $query2 = $this->db->query($sql2);
        $row2 = $query2->row();
        $tblName = $row2->table_name;
        $field_name = $row2->field_name;

        $sql = "SELECT c1.slug_url FROM ".$tblName." c1
                  WHERE c1.lang_id = " . $lang_id . "
                  AND c1.".$field_name." =
                  (SELECT c2.".$field_name." FROM ".$tblName." c2
                  WHERE c2.slug_url = '" . $slug . "')";

        $query = $this->db->query($sql);

        $row = $query->row();
        return $row->slug_url;
    }

    protected function getslugUrlStr() {
        $this->db->select("GROUP_CONCAT(c.slug_url) as slug_url");
        $this->db->from('cms c');
        $this->db->where("c.status", 1);
        $query = $this->db->get();
        $row = $query->row();

        return $row->slug_url;
    }



}
