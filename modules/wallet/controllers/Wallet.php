<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 

class Wallet extends CI_Controller { 
	
    public function __construct() {
        parent::__construct();
        $this->load->model('EmailModel');
    }


    public function index(){
        
	     
        $this->form_validation->set_rules('session_key', 'Authentication', 'trim|required|strtolower');  
        if($this->form_validation->run() == FALSE){ 
    
            $response = array(
                'response' => false,
                'message'  => validation_errors(), 
            );
            echo json_encode($response);
            return;

        }else {

            $userkey = $this->input->post('session_key'); 
            $sql = "
            SELECT 
                u.user_id, 
                u.user_names, 
                u.user_surname, 
                u.user_email, 
                u.user_thumb, 
                u.user_cellphone, 
                u.user_key,
                ar.account_role_name,
                w.wallet_id,
                w.balance,
                w.currency
            FROM `users` u
            LEFT JOIN `users_acl` a ON a.users_acl_userID = u.user_id
            LEFT JOIN `account_roles` ar ON ar.account_role_id = a.users_acl_roleID
            INNER JOIN `wallets` w ON w.user_id = u.user_id
            WHERE u.user_status = 'active' 
            AND a.users_acl_status = 'active' 
            AND u.user_key = ?  
            LIMIT 1
        ";
        
        // Execute the query with the username and hashed password
        $query = $this->db->query($sql, array($userkey));
        $user  = $query->row_array(); // Fetch user as an associative array

        // Check if a matching user was found
        if ($user) {
            // User found, return user data in JSON format
            $response = array(
                'response' => true,
                'message'  => 'Wallet retrieved ',
                'data'     => array( $user
                ),
               
            );

        


        } else {
            // No matching user found, return error message
            $response = array(
                'response' => false,
                'message'  => 'Invalid Token'
               
            );
        } 

        echo json_encode($response);


        }
        
    }// public function index(){
        
        public function validate_amount($amount) {
            // Ensure the amount is a valid number and greater than zero
            if (is_numeric($amount) && $amount > 0) {
                return true;
            } else {
                $this->form_validation->set_message('validate_amount', 'The Amount must be a positive number.');
                return false;
            }
        }


    public function addmoney(){

        $this->form_validation->set_rules('session_key', 'Authentication', 'trim|required|strtolower');  
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|callback_validate_amount'); 
        $this->form_validation->set_rules('currency', 'Currency', 'trim|strtolower');
          
        if($this->form_validation->run() == FALSE){ 
    
            $response = array(
                'response' => false,
                'message'  => validation_errors(), 
            );
            echo json_encode($response);
            return;

        }else {


            $userkey = $this->input->post('session_key'); 
            $sql = "
            SELECT 
                u.user_id, 
                u.user_names, 
                u.user_surname, 
                u.user_email, 
                u.user_thumb, 
                u.user_cellphone, 
                u.user_key,
                ar.account_role_name,
                w.wallet_id,
                w.balance,
                w.currency
            FROM `users` u
            LEFT JOIN `users_acl` a ON a.users_acl_userID = u.user_id
            LEFT JOIN `account_roles` ar ON ar.account_role_id = a.users_acl_roleID
            INNER JOIN `wallets` w ON w.user_id = u.user_id
            WHERE u.user_status = 'active' 
            AND a.users_acl_status = 'active' 
            AND u.user_key = ?  
            LIMIT 1
        ";
        
        // Execute the query with the username and hashed password
        $query = $this->db->query($sql, array($userkey));
        $user  = $query->row_array(); // Fetch user as an associative array
        $response  = array();
        // Check if a matching user was found
        if ($user) {
           
            

            $session_key = $this->input->post('session_key');
            $amount      = $this->input->post('amount'); 

            $sql = "
                UPDATE wallets
                SET balance = balance + ?
                WHERE wallet_id = (
                    SELECT wallet_id 
                    FROM wallets 
                    WHERE user_id = (
                        SELECT user_id 
                        FROM users 
                        WHERE user_key = ?
                    )
                )
            "; 

        $this->db->query($sql, array($amount, $session_key));


            $response = array(
                'response' => true,
                'message'  => 'wallet updated successful',
                'data'     => array( $user
                ),
               
            );

            $transactionsValues['wallet_id']   = $user['wallet_id'];
            $transactionsValues['transaction_typeID']   = 2;
            $transactionsValues['description']   = "Deposit of $amount was made ";
            $transactionsValues['amount']   = $amount;
            $transactionsValues['status']   = 'completed';
            

            $this->db->insert("transactions", $transactionsValues);


            $to       = $user['user_email'];
            $subject  = 'Transaction from the wallet app';
            $name     = $user['user_names'];
            $date     = date('Y-m-d');
            $body     = $transactionsValues['description'];
    
            $resemailNotificatioult = $this->EmailModel->sendEmail($to, $subject, $name, $date,$body);
    
            echo json_encode($response);

     





        } else {
            // No matching user found, return error message
            $response = array(
                'response' => false,
                'message'  => 'Invalid wallet'
               
            );
        } 

        echo json_encode($response);


        } 
    }

    public function withdrawmoney() {
        // Set validation rules
        $this->form_validation->set_rules('session_key', 'Authentication', 'trim|required|strtolower');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|greater_than[0]|callback_validate_amount');
        $this->form_validation->set_rules('currency', 'Currency', 'trim|strtolower');
    
        // Validate the input
        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'response' => false,
                'message'  => validation_errors(),
            );
            echo json_encode($response);
            return;
        } else {
            $session_key = $this->input->post('session_key');
            $amount = $this->input->post('amount');
    
            // Get the user's wallet details
            $sql = "
                SELECT 
                    u.user_id,
                    u.user_key,
                    w.wallet_id AS user_wallet_id,
                    w.balance AS user_balance
                FROM `users` u
                INNER JOIN `wallets` w ON w.user_id = u.user_id
                WHERE u.user_status = 'active'
                AND u.user_key = ?
                LIMIT 1
            ";
            $query = $this->db->query($sql, array($session_key));
            $user = $query->row_array();
    
            // Initialize response
            $response = array();
    
            if ($user) {
                // Check if the user has enough balance
                if ($user['user_balance'] >= $amount) {
                    // Begin transaction
                    $this->db->trans_start();
    
                    // Deduct from user's wallet
                    $sql = "
                        UPDATE wallets
                        SET balance = balance - ?
                        WHERE wallet_id = ?
                    ";
                    $this->db->query($sql, array($amount, $user['user_wallet_id']));
    
                    // Insert transaction for withdrawal
                    $transactionValues = array(
                        'wallet_id' => $user['user_wallet_id'],
                        'transaction_typeID' => 4, // Assuming 4 is for withdrawal
                        'description' => "Withdrawal of $amount",
                        'amount' => $amount,
                        'status' => 'completed'
                    );
                    $this->db->insert('transactions', $transactionValues);
    

                    $to       = $user['user_email'];
                    $subject  = 'Transaction from the wallet app';
                    $name     = $user['user_names'];
                    $date     = date('Y-m-d');
                    $body     = "Withdrawal of $amount";
            
                    $resemailNotificatioult = $this->EmailModel->sendEmail($to, $subject, $name, $date,$body);


                    // Commit transaction
                    $this->db->trans_complete();
    
                    if ($this->db->trans_status() === FALSE) {
                        // Rollback transaction
                        $this->db->trans_rollback();
                        $response = array(
                            'response' => false,
                            'message'  => 'Transaction failed. Please try again.',
                        );
                    } else {
                        $response = array(
                            'response' => true,
                            'message'  => 'Withdrawal successful',
                        );
                    }
                } else {
                    $response = array(
                        'response' => false,
                        'message'  => 'Insufficient balance',
                    );
                }
            } else {
                $response = array(
                    'response' => false,
                    'message'  => 'Invalid wallet',
                );
            }
    
            echo json_encode($response);
        }
    }
    

    public function transfermoney() {
       
        $this->form_validation->set_rules('session_key', 'Authentication', 'trim|required|strtolower');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|numeric|greater_than[0]|callback_validate_amount');
        $this->form_validation->set_rules('recipient_email', 'Recipient Email', 'trim|required|valid_email');
    
        // Validate the input
        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'response' => false,
                'message'  => validation_errors(),
            );
            echo json_encode($response);
            return;
        } else {
            $session_key = $this->input->post('session_key');
            $amount = $this->input->post('amount');
            $recipient_email = $this->input->post('recipient_email');
    
            // Begin transaction
            $this->db->trans_start();
    
            // Get the sender's wallet details
            $sql = "
                SELECT 
                    u.user_id AS sender_user_id,
                    u.user_key AS sender_user_key,
                    w.wallet_id AS sender_wallet_id,
                    w.balance AS sender_balance
                FROM `users` u
                INNER JOIN `wallets` w ON w.user_id = u.user_id
                WHERE u.user_status = 'active'
                AND u.user_key = ?
                LIMIT 1
            ";
            $query = $this->db->query($sql, array($session_key));
            $sender = $query->row_array();
    
            if ($sender) {
                // Check if the sender has enough balance
                if ($sender['sender_balance'] >= $amount) {
                    // Get the recipient's wallet details
                    $sql = "
                        SELECT 
                            w.wallet_id AS recipient_wallet_id
                        FROM `users` u
                        INNER JOIN `wallets` w ON w.user_id = u.user_id
                        WHERE u.user_status = 'active'
                        AND u.user_email = ?
                        LIMIT 1
                    ";
                    $query = $this->db->query($sql, array($recipient_email));
                    $recipient = $query->row_array();
    
                    if ($recipient) {
                        // Deduct from sender's wallet
                        $sql = "
                            UPDATE wallets
                            SET balance = balance - ?
                            WHERE wallet_id = ?
                        ";
                        $this->db->query($sql, array($amount, $sender['sender_wallet_id']));
    
                        // Add to recipient's wallet
                        $sql = "
                            UPDATE wallets
                            SET balance = balance + ?
                            WHERE wallet_id = ?
                        ";
                        $this->db->query($sql, array($amount, $recipient['recipient_wallet_id']));
    
                        // Insert transaction for sender
                        $transactionValuesSender = array(
                            'wallet_id' => $sender['sender_wallet_id'],
                            'transaction_typeID' => 5, // Assuming 5 is for transfer out
                            'description' => "Transferred $amount to $recipient_email",
                            'amount' => $amount,
                            'status' => 'completed'
                        );
                        $this->db->insert('transactions', $transactionValuesSender);
    
                        // Insert transaction for recipient
                        $transactionValuesRecipient = array(
                            'wallet_id' => $recipient['recipient_wallet_id'],
                            'transaction_typeID' => 6, // Assuming 6 is for transfer in
                            'description' => "Received $amount from " . $sender['sender_user_key'],
                            'amount' => $amount,
                            'status' => 'completed'
                        );
                        $this->db->insert('transactions', $transactionValuesRecipient);


                        $to       = $user['user_email'];
                        $subject  = 'Transaction from the wallet app';
                        $name     = $user['user_names'];
                        $date     = date('Y-m-d');
                        $body     = "Received $amount from " . $sender['sender_user_key'];
                
                        $resemailNotificatioult = $this->EmailModel->sendEmail($to, $subject, $name, $date,$body);
    
                        // Commit transaction
                        $this->db->trans_complete();
    
                        if ($this->db->trans_status() === FALSE) {
                            // Rollback transaction
                            $this->db->trans_rollback();
                            $response = array(
                                'response' => false,
                                'message'  => 'Transaction failed. Please try again.',
                            );
                        } else {
                            $response = array(
                                'response' => true,
                                'message'  => 'Transfer successful',
                            );
                        }
                    } else {
                        $response = array(
                            'response' => false,
                            'message'  => 'Recipient wallet not found',
                        );
                    }
                } else {
                    $response = array(
                        'response' => false,
                        'message'  => 'Insufficient balance',
                    );
                }
            } else {
                $response = array(
                    'response' => false,
                    'message'  => 'Invalid sender wallet',
                );
            }
    
            echo json_encode($response);
        }
    }

    public function viewtransactions() {
        // Set validation rules
        $this->form_validation->set_rules('session_key', 'Authentication', 'trim|required|strtolower');
    
        // Validate the input
        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'response' => false,
                'message'  => validation_errors(),
            );
            echo json_encode($response);
            return;
        } else {
            $session_key = $this->input->post('session_key');
    
            // Begin transaction
            $this->db->trans_start();
    
            // Get the user's wallet details
            $sql = "
                SELECT 
                    u.user_id,
                    u.user_key,
                    w.wallet_id,
                    w.balance,
                    w.currency
                FROM `users` u
                INNER JOIN `wallets` w ON w.user_id = u.user_id
                WHERE u.user_status = 'active'
                AND u.user_key = ?
                LIMIT 1
            ";
            $query = $this->db->query($sql, array($session_key));
            $user_wallet = $query->row_array();
    
            if ($user_wallet) {
                // Fetch the transactions for the wallet, including transaction type names
                $sql = "
                    SELECT 
                        t.transaction_id,
                        t.transaction_typeID,
                        tt.transaction_type_name AS transaction_type,
                        t.description,
                        t.amount,
                        t.status,
                        t.transaction_date
                    FROM `transactions` t
                    INNER JOIN `TransactionTypes` tt ON tt.transaction_type_id = t.transaction_typeID
                    WHERE t.wallet_id = ?
                    ORDER BY t.transaction_date DESC
                ";
                $query = $this->db->query($sql, array($user_wallet['wallet_id']));
                $transactions = $query->result_array();
    
                // Commit transaction
                $this->db->trans_complete();
    
                if ($this->db->trans_status() === FALSE) {
                    // Rollback transaction
                    $this->db->trans_rollback();
                    $response = array(
                        'response' => false,
                        'message'  => 'Error fetching data. Please try again.',
                    );
                } else {
                    $response = array(
                        'response' => true,
                        'message'  => 'Data fetched successfully',
                        'data'     => array(
                            'balance'       => $user_wallet['balance'],
                            'currency'      => $user_wallet['currency'],
                            'transactions'  => $transactions
                        ),
                    );

                    
                }
            } else {
                $response = array(
                    'response' => false,
                    'message'  => 'Invalid session key or wallet not found',
                );
            }
    
            echo json_encode($response);
        }
    }
    
    
    
     
}// class Site extends CI_Controller {
