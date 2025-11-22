<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Travelled extends CI_Controller 
{
    public function index()
    {
        $this->load->model('TravelledModel');
        return $this->TravelledModel->get_Travelled_data();
    }
    public function get_search_Travelled_data()
    {
        $this->load->model('TravelledModel');
        return $this->TravelledModel->get_search_Travelled_data();
    }
    public function download_data()
    {
        $this->load->model('TravelledModel');
        return $this->TravelledModel->download_data();
    }
}
?>