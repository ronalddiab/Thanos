<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function widget($name = '',$options = array()) {
    
    $ci = & get_instance();
    $ci->load->widget($name);    
    echo $ci->$name->run($options);
}