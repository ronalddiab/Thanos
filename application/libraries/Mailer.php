<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('PHPMailer/class.phpmailer.php');

class Mailer extends PHPMailer
{

    var $mail;
    
    public function __construct()
    {           
        // the true param means it will throw exceptions on errors, which we need to catch
        $this->mail = new PHPMailer(true);
 
        $this->mail->IsSMTP(); // telling the class to use SMTP
 
        $this->mail->CharSet = "utf-8";                  // ????? CharSet ????????
        $this->mail->SMTPDebug  = 0;                     // enables SMTP debug information
        if(SMTP_USE_SERVER){
            $this->mail->SMTPAuth   = SMTP_AUTH;                  // enable SMTP authentication
            $this->mail->SMTPSecure = SMTPSecure;                 // sets the prefix to the servier
            $this->mail->Host       = SMTP_HOST;      // sets GMAIL as the SMTP server
            $this->mail->Port       = SMTP_PORT;                   // set the SMTP port for the GMAIL server
            $this->mail->Username   = SMTP_USERNAME;// GMAIL username
            $this->mail->Password   = SMTP_PASSWORD;       // GMAIL password
        }
        //$this->mail->AddReplyTo('YOUR_GAMIL@gmail.com', 'YOUR_NAME');
        $this->mail->SetFrom(SITE_FROM_EMAIL, SITE_NAME);
    }

    public function sendmail($to, $to_name, $subject, $body){

        try{
            $this->mail->AddAddress($to, $to_name);
            //$this->mail->AddBCC('testsoft.54@gmail.com','CIDEMO Mails');

            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            $this->mail->Send();

            echo "Message Sent OK</p>\n";
 
        } catch (phpmailerException $e) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }
}
 
/* End of file mailer.php */