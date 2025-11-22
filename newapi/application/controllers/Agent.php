<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent extends CI_Controller 
{
    public function index()
    {
        $this->load->model('AgentModel');
        return $this->AgentModel->get_Agent_data();
    }
    public function get_search_Agent_data()
    {
        $this->load->model('AgentModel');
        return $this->AgentModel->get_search_Agent_data();
    }
    public function add_Agent()
    {
        $this->load->model('AgentModel');
        return $this->AgentModel->add_Agent();
    }
    public function get_suppliers()
    {
        $this->load->model('AgentModel');
        return $this->AgentModel->get_suppliers();
    }
}
?>