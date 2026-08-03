<?php
###################################################################
##
##	Pagination Config Variable File
##	Version: 0.01
##
##	Last Edit:
##	15-8-2013
##
##	Description:
##	Pagination config class for load config variable
##
##	Author:
##	By Mehul Panchal
##
##################################################################
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  
        $config['pagination']['base_url'] = '';   // url for pass to pagination
        
        $config['pagination']['num_links'] = 3;   // display how much link to diplay before and after
        
        $config['pagination']['isFunctionRequest'] = 0;  // set this variable for post pagination method
        
        $config['pagination']['functionWithParams'] = '';  // for passing function wiith paramter to this varibale for post method pagination
        
        $config['pagination']['total_records'] = 0;   // total records of the table
        
        $config['pagination']['page_number'] = 1;  // set default page_number for pagination
        
        $config ['pagination']['isAjaxRequest'] = 0;  // set 1 for ajax pagination method
        
        $config ['pagination']['section_name'] = '';  // set 1 for ajax pagination method
        
/* End of file pagination.php */
/* Location: /application/config/pagination.php */