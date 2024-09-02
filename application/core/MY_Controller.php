<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Controller extends CI_Controller {
    
    var $_session;    
    var $_userID;
    var $_usernames;

    function __construct() {
		
	parent::__construct();  
        
         // Save current URI to session
        $this->session->set_userdata('loginReturnURL', base_url(uri_string()));        
        $session          = $this->session->userdata('logged_in');         
        
        
        $this->_userID    = $session['member_id'];
        $this->_usernames = $session['names']; 
        $this->_session   = $session;  
        
    }// function __construct() {
    
    
    public function dateToTimestamp($date){
                       
        $dataArray = explode("-", $date);
        $results   = mktime(0,0,0, $dataArray[1],$dataArray[2], $dataArray[0]);
        return $results;  
        
    }// public function dateToTimestamp($date){
    
    public function custom_print($data){        
        
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        
    }// public function custom_print($data){  
    
}// class MY_Controller extends CI_Controller {



class MY_SubscriberController extends MY_Controller {
    
    function __construct() {
        
        parent::__construct();        
        
        $allowedUsersTypes = array("admin", "whistle_blower");
        
        if(!in_array($this->_session['member_type'],$allowedUsersTypes)){
            redirect('report', 'refresh');
            exit;
        }// if($this->_session['member_type'] != 'subscriber'){
        
    }// function __construct() {   
    
}//  class MY_SubscriberController extends MY_Controller {


class MY_AdminController extends MY_Controller {
    
    function __construct() {
        
        parent::__construct();        
        
        $allowedUsersTypes = array("admin");
        
        if(!in_array($this->_session['member_type'], $allowedUsersTypes)){
            redirect('admin/login', 'refresh');
            exit;
        }// if($this->_session['member_type'] != 'subscriber'){
        
    }// function __construct() {   
    
}//  class MY_SubscriberController extends MY_Controller {