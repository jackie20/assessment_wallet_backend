<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use GuzzleHttp\Client;

class Site extends CI_Controller { 
	
    public function index(){
        
	    $this->load->view('common/site_header');
        $this->load->view('index/index_view');
        $this->load->view('common/site_footer');
        
    }// public function index(){
        
     
}// class Site extends CI_Controller {
