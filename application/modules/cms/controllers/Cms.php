<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *  CMS Controller
 *
 *  CMS controller to display cms page in front site.
 *  Also dispaly default page of cms.
 * 
 * @package CIDemoApplication
 *  
 * @copyright	(c) 2013, TatvaSoft
 * @author Amit Patel <amit.patel@sparsh.com>
 */
class Cms extends Base_Front_Controller
{
    /*
     * Create an instance
    */
    function __construct()
    {
        parent::__construct();
        //load helpers
        $this->load->helper(array('url', 'cookie'));
        //load theme        
        $this->theme->set_theme("front");              
    }

    /**
     * action to display language wise cms page based on slug url
     * @param string $slug_url
     */
    function index($slug_url = NULL)
    {        
        
        //Type Casting
        $slug_url = trim(strip_tags($slug_url));

        //Initialize
        $data = array();
        $cms_list = array();

        //Logic  
        $cms_list = $this->cms_model->get_cms_detail_by_slug($slug_url);

        if (count($cms_list) <= 0 || $this->input->post('select_language') != '')
        {
            $data_cms = $this->cms_model->get_cms_id_from_slug_url($slug_url);
            if (count($data_cms))
            {
                $cms_list = $this->cms_model->get_cms_detail_by_id($data_cms[0]['c']['cms_id'], $this->session->userdata[$this->section_name]['site_lang_id']);
                if (count($cms_list))
                {
                    redirect("/" . $cms_list[0]['c']['slug_url']);
                } else
                {
                    $this->theme->set_message(lang('cms_language_change_error'), 'error');
                    redirect("/");
                }
            }
        }
        if (count($cms_list) <= 0)
        {
            $this->theme->set_message(lang('cms_language_change_error'), 'error');
            redirect("/");
        }
        //Variable assignments to view
        $data = $cms_list;
        $this->theme->set('cms', $data);
        $this->theme->set_page_title($cms_list[0]['cm']['meta_title']);
        
        $this->theme->set_meta(array('name'=>'description', 'content'=>$cms_list[0]['cm']['meta_description']));
        $this->theme->set_meta(array('name'=>'keywords', 'content'=>$cms_list[0]['cm']['meta_keywords']));
        //message cookie?
        $message = get_cookie('message');
        if ($message)
        {
            //yes... add message to the theme
            $this->theme->add_message($message, 'success');
            //wipe the cookie
            set_cookie('message', null, null);
        }
        //load the theme_example view
        $this->theme->set_layout('layout');
        $this->theme->view();
    }

    /**
     * action to display default cms page
     *
     */
    function defaultpage()
    {
        // delete below line after demo starts
        $data = array();        
        $this->theme->view($data, 'home');
        // delete below line after demo ends
    }

}
