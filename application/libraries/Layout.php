<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Layout {

    public function __construct()
    {
       global $obj;
       global $layout;
    }

    public function Layout($layout = "home_page_layout"){
        $this->obj =& get_instance();
        $this->layout = $layout;
    }

    public function setLayout($layout){
        $this->layout = $layout;
    }

    public function view($view, $data=null, $return=false){
        $loadedData['content_for_layout'] = $this->obj->load->view($view,$data,true);
        if($return):
            $output = $this->obj->load->view('layouts/front/'.$this->layout, $loadedData, true);
            return $output;
        else:
            $this->obj->load->view('layouts/front/'.$this->layout, $loadedData, false);
        endif;
    }
   
}
