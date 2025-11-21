<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetData extends CI_Controller 
{
    public function __construct()
{
    parent::__construct();
    $this->load->database(); // <-- make sure this is here
}
    public function get_list()
    {
        $this->load->model('GetDataModel');
        return $this->GetDataModel->get_list();
    }
    public function get_person_list()
    {
        $this->load->model('GetDataModel');
        return $this->GetDataModel->get_person_list();
    }

    public function check_supplier_name()
{
    //echo "hh";die;
    $name = $this->input->post('name');
    //echo $name;die;
    $this->db->where('name', $name);
    $query = $this->db->get('supplier');
    //print_r($query);die;
    echo json_encode(['exists' => $query->num_rows() > 0]);
}

public function check_edit_supplier_name()
{
    $name = $this->input->post('name');
    $id = $this->input->post('id');

    // Check if name exists, excluding the current record
    $this->db->where('name', $name);
    if (!empty($id)) {
        $this->db->where('id !=', $id); // Exclude current ID
    }
    $query = $this->db->get('supplier');

    echo json_encode(['exists' => $query->num_rows() > 0]);
}




    public function insert_data()
    {
        $this->load->model('GetDataModel');
        return $this->GetDataModel->insert_data();
    }
    
    // public function personDetail()
    // {
    //     $this->load->model('GetDataModel');
    //     return $this->GetDataModel-> personDetail();
    // }
    // public function addperson()
    // {
    //     $this->load->model('GetDataModel');
    //     return $this->GetDataModel->addperson();
    // }
    //   public function getperson()
    // {
    //     $this->load->model('GetDataModel');
    //     return $this->GetDataModel->getperson();
    // }
    public function delete_data()
    {
        $this->load->model('GetDataModel');
        return $this->GetDataModel->delete_data();
    }
    
    public function create_Image_Json()
    {
        $this->load->model('GetDataModel');
        return $this->GetDataModel->create_Image_Json();
    }
}
?>