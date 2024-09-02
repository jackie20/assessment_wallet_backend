<?php


class LoginSession extends CI_Model {

    public function __construct() {
      
	parent::__construct();	  
    }//  public function __construct() {
    
    
    public function start($member_id, $member_names, $member_status, $member_type){
        
        $data['member_id']    = $member_id;
        $data['names']        = $member_names;
        $data['status']       = $member_status;
        $data['member_type']  = $member_type;
        session_regenerate_id();
        $this->session->set_userdata('logged_in', $data);
        return;
        
    }// public function create($userID, $userNames){
    
}// class LoginSession extends CI_Model {
