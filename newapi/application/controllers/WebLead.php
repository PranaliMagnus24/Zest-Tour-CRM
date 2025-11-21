<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WebLead extends CI_Controller 
{
    public function index()
    {
        $this->load->model('WebLeadModel');
        return $this->WebLeadModel->get_WebLead();
    }
}
?>