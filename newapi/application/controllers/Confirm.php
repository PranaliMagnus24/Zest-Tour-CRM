<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Confirm extends CI_Controller 
{
    public function index()
    {
        $this->load->model('ConfirmModel');
        return $this->ConfirmModel->get_Confirm_data();
    }
    public function get_search_Confirm_data()
    {
        $this->load->model('ConfirmModel');
        return $this->ConfirmModel->get_search_Confirm_data();
    }
    public function download_data()
    {
        $this->load->model('ConfirmModel');
        return $this->ConfirmModel->download_data();
    }
}
?>