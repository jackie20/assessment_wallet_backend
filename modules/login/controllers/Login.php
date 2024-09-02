<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Login extends CI_Controller {
 
    public function index(){
        
 
        $this->form_validation->set_rules('username', 'Username', 'trim|required|strtolower');
        $this->form_validation->set_rules('password', 'Password', 'trim|strtolower|required');
          
        if($this->form_validation->run() == FALSE){ 
    
            $response = array(
                'response' => false,
                'message'  => validation_errors(), 
            );
            echo json_encode($response);
            return;

        }else {

            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $hashed_password = hash("sha512", $password);

            $sql = "
            SELECT 
                user_id, user_names, user_surname, user_email, 
                user_thumb, user_cellphone ,user_key
                 , account_role_name
            FROM `users` u
            LEFT JOIN `users_acl` a ON a.users_acl_userID = u.user_id
            LEFT JOIN `account_roles` ar ON ar.account_role_id = a.users_acl_roleID
            WHERE user_status = 'active' 
            AND users_acl_status = 'active' 
            AND users_acl_password = ? 
            AND user_email = ?
            LIMIT 1
        ";

        // Execute the query with the username and hashed password
        $query = $this->db->query($sql, array($hashed_password, $username));
        $user  = $query->row_array(); // Fetch user as an associative array

        // Check if a matching user was found
        if ($user) {
            // User found, return user data in JSON format
            $response = array(
                'response' => true,
                'message'  => 'Login successful',
                'data'     => array( $user
                ),
               
            );
        } else {
            // No matching user found, return error message
            $response = array(
                'response' => false,
                'message'  => 'Invalid login credentials.'
               
            );
        } 

        echo json_encode($response);


        }
        
    }// public function index(){

 
    
        public function register(){

 
            $this->form_validation->set_rules('names', 'First Names', 'trim|required|strtolower');  
            $this->form_validation->set_rules('surname', 'Last Name', 'trim|required|strtolower'); 
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_email'); 
            $this->form_validation->set_rules('cellphone', 'Cellphone', 'trim|required|callback_check_cellphone'); 
            $this->form_validation->set_rules('password', 'Password', 'trim|required');  
            $this->form_validation->set_rules('password_conf', 'Confirm Password', 'trim|required|matches[password]'); 
            
             
        
            if($this->form_validation->run() == FALSE){
                 
                $response = array(
                    'response' => false,
                    'message'  => validation_errors(), 
                );
                echo json_encode($response);
                return;
                
            }else {
                
                 
                $now                             = time();
                $link                            = md5($this->input->post('cellphone').md5($now.time()));
                $userValues['user_names']        = $this->input->post('names');
                $userValues['user_surname']      = $this->input->post('surname');
                $userValues['user_email']        = $this->input->post('email');  
                
                
                $userValues['user_dateCreated']  = $now;
                $userValues['user_resetKey']     = md5($now.$this->input->post('email').$now);
                $userValues['user_key']          = md5($now);
                $userValues['user_cellphone']    = $this->input->post('cellphone'); 
                $userValues['user_dateModified'] = $now;  
                
                
                $this->db->insert("users", $userValues);
                $userID = $this->db->insert_id();
                
                 
                $aclValues['users_acl_roleID']      = 2;   
                $aclValues['users_acl_userID']      = $userID;
                $aclValues['users_acl_password']    = hash("sha512", $this->input->post('password')); 
                $this->db->insert("users_acl", $aclValues);
                 
                $walletValues['user_id']   = $userID;
                $this->db->insert("wallets", $walletValues);
                $walletId = $this->db->insert_id();

                $transactionsValues['wallet_id']   = $walletId;
                $transactionsValues['transaction_typeID']   = 1;
                $transactionsValues['description']   = "Account was Created ";
                $transactionsValues['amount']   = 0;
                $transactionsValues['status']   = 'completed';
                

                $this->db->insert("transactions", $transactionsValues);
                
   
                $response = array(
                    'response' => true,
                    'message'  => 'account was created successful',
                    'data'     => array( $userValues
                    ),
                
                );

                echo json_encode($response);
            
            }//if($this->form_validation->run() == FALSE){

        }

        
    function check_cellphone($cellphone){
              
	
        $cellphone = preg_replace("/[^0-9,.]/","",$cellphone);
        
            $sql       = "SELECT user_id FROM `users` WHERE user_cellphone='".$cellphone."' and user_status = 'active' ";
            $query     = $this->db->query($sql);
            $rows      = $query->result_array();
          
        if($rows){	
                  
               
                    $this->form_validation->set_message('check_cellphone', 'Cellphone already in use.');
                    return false;
                     
        }else{	  
                return true;		
        }// if(filter_var($username, FILTER_VALIDATE_EMAIL)){
            
        }// function check_cellphone($username){
         
        function check_email($email){    
        
                $sql       = "SELECT user_id FROM `users` WHERE user_email='".$email."' and user_status = 'active' ";
                $query     = $this->db->query($sql);
                $rows      = $query->result_array();
                
                if($rows){
                    
                
                    $this->form_validation->set_message('check_email', 'Email address already in use.');
                    return false;
                      
                }else{
                    
                    return true;	

                }// if($rows){
                 
            
        }// function check_email($email){

        
     
}// class Login extends CI_Controller {
    