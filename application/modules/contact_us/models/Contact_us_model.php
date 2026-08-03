<?php

###################################################################
##
##	Modules Admin Module
##	Version: 0.01
##
##	Last Edit:
##	July 25 2013
##
##	Description:
##	Modules management.
##	
##	Author:
##	By Mehul Panchal
##	
##	Comments:
##	Updated set configuration on modules
##
##################################################################

class Contact_US_model extends CI_Model {
    protected $_tabelname = 'contact_us';
   
    function Contact_US_model() {
        parent::__construct();
    }
    
    
    function save_contact_form($data)
    {
             $this->db->insert($this->_tabelname, $data);
    }
    function send_email($data)
    {
                    $config = Array(
                        'protocol' => 'smtp',
                        'smtp_host' => SMTP_HOST,
                        'smtp_port' => SMTP_PORT,
                        'smtp_user' => SMTP_USERNAME,
                        'smtp_pass' => SMTP_PASSWORD,
                        'mailtype' => "html"

                    );
                            
                    $this->email->initialize($config);   
                    
                    
                   
                    // send email to admin 
                    
                    $this->email->from(SITE_FROM_EMAIL, "Contact Us Details");
                    $this->email->to(CONTACT_US_EMAIL,"ADMIN");             
                    $this->email->subject('Contact Us');                    
                    $this->email->message($this->load->view("email_template",$data,true));
                    $this->email->send();
                    
                    
                    // send email to user
                    
                    
                    $this->email->from(SITE_FROM_EMAIL,"Contact Us Details");
                    $this->email->to($this->input->post("email"));             
                    $this->email->subject('Contact Us');
                    $this->email->message($this->load->view("email_template",$data,true));
                    $this->email->send();
    }
    

}

