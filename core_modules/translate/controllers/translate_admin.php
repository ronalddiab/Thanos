<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Translate Admin Controller
 *
 *  Translate admin module for add or update translation
 * 
 * @package CIDemoApplication
 *  
 * @copyright	(c) 2013, TatvaSoft
 * @author Pankit Mehta <pankit.mehta@sparsh.com>
 */
class Translate_admin extends Base_Admin_Controller
{

    /**
     * Array of current languages
     *
     * @var array
     */
    private $langs;

    /**
     * Loads required classes
     *
     * @todo Add permission restrictions
     *
     * @return void
     */
    function Translate_admin()
    {
        parent::__construct();
         //Logic
        $this->access_control($this->access_rules());
        
        $this->lang->load('translate');
        $this->load->helper('languages');
        $this->langs = list_languages();
        $this->breadcrumb->add('Translate', base_url().$this->section_name.'/translate');
    }

    /**
     * Function for set permission
     * @return array
     */
    private function access_rules()
    {
        return array(
                     array(
                          'actions' => array('index','edit','save'),
                          'users' => array('@'),
                     )
            );
    }
    
    /**
     * Displays a list of all core language files, as well as a list of
     * modules that the user can choose to edit.
     *
     * @access public
     *
     * @return void
     */
    public function index($trans_lang = 'english')
    {
        //check selected language
        if($this->input->post('select_lang'))
        {
            //variable assignment
            $trans_lang = $this->input->post('trans_lang');
            if($trans_lang == 'other')
            {
                $trans_lang = $this->input->post('new_lang');
            }
        }
        //Logic
        if(!in_array($trans_lang, $this->langs))
        {
            $this->langs[] = $trans_lang;
        }

        $all_lang_files = list_lang_files(); //get language's file list
        // check that we have custom modules
        if(isset($all_lang_files['custom']))
        {
            $custom_modules = $all_lang_files['custom'];
        }

        //Pass variable to view
        $data['languages'] = $this->langs;
        $data['lang_files'] = $all_lang_files['core'];
        $data['trans_lang'] = $trans_lang;
        $data['modules'] = $custom_modules;

        //Create page-title
        $this->theme->set('page_title', ucfirst($this->_module));

        //Render view
        $this->theme->view($data);
    }

    //end index()

    /**
     * Allow the user to edit a language file
     *
     * @access public
     *
     * @return void
     */
    public function edit($trans_lang = '', $lang_file = '')
    {
        // Logic
        if($lang_file && $this->input->post('submit'))
        {
            if(save_lang_file($lang_file, $trans_lang, $_POST['lang']))
            {

                $this->theme->set_message(lang('tr_save_success'), 'success');
                redirect($this->section_name.'/translate/index/' . $trans_lang);
            }
            else
            {
                $this->theme->set_message(lang('tr_save_fail'), 'error');
            }
        }

        // Get the language file
        if($lang_file)
        {
            $orig = load_lang_file($lang_file, 'english');
            $new = load_lang_file($lang_file, $trans_lang);

            if(!$new)
            {
                $new = $orig;
            }
            //Pass variable to view
            $data['orig'] = $orig;
            $data['new'] = $new;
            $data['lang_file'] = $lang_file;
            $data['trans_lang'] = $trans_lang;
        }

        //Create page-title
        $this->theme->set('page_title', lang('tr_edit_title'));

        //Render view
        $this->theme->view($data);
    }

}