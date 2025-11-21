<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
    public function index()
    {
        $this->load->model('DashboardModel');
        return $this->DashboardModel->get_Dashboard_data();
    }
}
?>