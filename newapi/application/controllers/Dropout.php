<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dropout extends CI_Controller 
{
    public function index()
    {
        $this->load->model('DropoutModel');
        return $this->DropoutModel->get_Dropout_data();
    }
    public function get_search_Dropout_data()
    {
        $this->load->model('DropoutModel');
        return $this->DropoutModel->get_search_Dropout_data();
    }
    public function download_data()
    {
        $this->load->model('DropoutModel');
        return $this->DropoutModel->download_data();
    }
}
?>