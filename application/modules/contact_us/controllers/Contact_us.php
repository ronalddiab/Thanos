<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Contact Us Front Controller
 *
 * Contact Us Controller for contact form email functionlaity
 *
 * @package CIDemoApplication
 * @subpackage Contact Us
 * @copyright	(c) 2013, TatvaSoft
 * @author Mehul Panchal <mehul.panchal@sparsh.com>
 */
class Contact_Us extends Base_Front_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->library('email');
        $this->load->library('form_validation');
        $this->load->helper(array('url'));
    }

    function index()
    {

        $CI = & get_instance();
        $flag = 0;

        $data = array(
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "subject" => $this->input->post("subject"),
            "message" => $this->input->post("message"),
            "ip_address" => $this->input->ip_address(),
            "timestamp" => date("Y-m-d H:i:s"),
            "ci" => $CI
        );


        if ($this->input->post("contact_submit"))
        {
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('message', 'Message', 'trim|required');
            if (CAPTCHA_SETTING)
                $this->form_validation->set_rules('captcha', 'Captcha', 'required|validate_captcha[' . $this->input->post("captcha") . ']');


            
            if ($this->form_validation->run())
            {
                unset($data["captcha"]);
                unset($data["ci"]);
                $this->contact_us_model->save_contact_form($data);
                $this->send_email($data);
                $flag = 1;

            }
            else
            {
                $data = array(
                    "name" => $this->input->post("name"),
                    "email" => $this->input->post("email"),
                    "subject" => $this->input->post("subject"),
                    "message" => $this->input->post("message"),
                    "ip_address" => $this->input->ip_address(),
                    "timestamp" => date("Y-m-d H:i:s"),
                    "captcha" => $this->my_captcha->deleteImage()->createWord()->createCaptcha(),
                    "ci" => $CI
                );
            }
        }
        else
        {

            $data = array("name" => "",
                "email" => "",
                "subject" => "",
                "message" => "",
                "ip_address" => "",
                "timestamp" => "",
                "captcha" => $this->my_captcha->deleteImage()->createWord()->createCaptcha(),
                "ci" => $CI
            );
        }

        //$this->theme->set_theme("front");
        if ($flag == 1)
        {
            //$this->theme->view($data, "sucucess_message");
            $this->theme->set_message(lang('detail_sent'), 'success');
            redirect(site_url().'contact_us/');

        }
        else
        {
            $this->theme->view($data, "contact_form");
        }
    }

    function recaptcha()
    {
        $data['captcha'] = $this->my_captcha->deleteImage()->createWord()->createCaptcha();
        echo $data['captcha'];
        exit;
    }

    /**
     * Function send email to user
     * @params $data for sending email
     */
    public function send_email($data = array(), $template = NULL) {

        $this->load->library('mailer');

        $mail->Mailer = "smtp";

        $this->mailer->mail->SetFrom($data['email'], SITE_NAME);
        $this->mailer->mail->IsHTML(true);
        $mail_vars = array(
            'Email' => $data['email'],
            'Name' => $data['name'],
            'Message' => $data['message'],
            'SITE_NAME' => SITE_NAME,
            'YEAR' => date('Y'),
            'logopath' => site_base_url() . 'themes/default/images/logo.jpg'
        );

        $body = get_template_body($this, 'contact_us', $mail_vars, $this->session->userdata[$this->section_name]['site_lang_id']);
        $subject = 'Contact us -'.$data['subject'];//get_template_subject($this, 'registration_template');

		if(trim($body)=="")
		{
			 //$this->theme->set_message(lang('email-template-inactive'), 'info');
			 //redirect(site_url().'users/login');
			 return false;
		}
		else
		{    

			try {
				$this->mailer->sendmail(CONTACT_US_EMAIL, SITE_NAME, $subject, $body);
			} catch (phpmailerException $e) {
				echo $e->errorMessage();
				exit;
			}
		   	return true;
		}
    }


}
