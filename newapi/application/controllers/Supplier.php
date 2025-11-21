<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller 
{
    public function index()
    {
        $this->load->model('SupplierModel');
        return $this->SupplierModel->get_Supplier_data();
    }
    public function get_search_Supplier_data()
    {
        $this->load->model('SupplierModel');
        return $this->SupplierModel->get_search_Supplier_data();
    }
    public function get_country_Supplier_data()
    {
        $this->load->model('SupplierModel');
        return $this->SupplierModel->get_country_Supplier_data();
    }
    public function add_Supplier()
    {
        $this->load->model('SupplierModel');
        return $this->SupplierModel->add_Supplier();
    }
}
?>