<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->library('email');
    }

    public function sendEmail($to, $subject, $name, $date,$body) {
        // Path to the email template
        $template_path = APPPATH . '../email_templates/email.html';
        
        // Load the HTML template
        if (!file_exists($template_path)) {
            return array('response' => false, 'message' => 'Email template not found');
        }
        
        $template = file_get_contents($template_path);

        // Replace placeholders with actual values
        $template = str_replace('%NAMES%', $name, $template);
        $template = str_replace('%BODY%', $body, $template);
        $template = str_replace('%DATE%', $date, $template);

        // Email configuration
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'gmail_email@gmail.com',
            'smtp_pass' => 'password_here',
            'mailtype'  => 'html',
            'charset'   => 'iso-8859-1',
            'wordwrap'  => TRUE,
            'smtp_crypto' => 'tls' 

        );
        
        $this->email->initialize($config);

        // Set email parameters
        $this->email->from('gmail_email Here', 'Wallet App');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($template);

        // Send email
        if ($this->email->send()) {
            return array('response' => true, 'message' => 'Email sent successfully');
        } else {
            return array('response' => false, 'message' => $this->email->print_debugger());
        }
    }
}
