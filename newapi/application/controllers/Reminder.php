<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reminder extends CI_Controller 
{
    public function index()
    {
        
        $this->load->model('RemainderModel');
        return $this->RemainderModel->get_Reminder_data();
    }
    public function addReminder()
    {
        $this->load->model('RemainderModel');
        return $this->RemainderModel->addReminder();
    }
    
}
?>