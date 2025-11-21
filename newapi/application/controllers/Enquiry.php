<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enquiry extends CI_Controller 
{
    public function index()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_Enquiry_data();
    }
    public function get_search_Missing_Reminder()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_search_Missing_Reminder();
    }
    public function getMissingReminder()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->getMissingReminder();
    }
    public function get_missing_count()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_missing_count();
    }
    
    public function presale_call()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->presale_call();
    }
    
    public function get_Enquiry_detail()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_Enquiry_detail();
    }
    public function addEnquiry()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->addEnquiry();
    }
    public function get_search_Enquiry_data()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_search_Enquiry_data();
    }
    public function get_search_dob()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->get_search_dob();
    }
    public function website_lead()
    {
        $this->load->model('EnquiryModel');
        return $this->EnquiryModel->website_lead();
    }

    public function add_customer_form()
{
    $this->load->model('EnquiryModel');
    $data['countries'] = $this->EnquiryModel->get_countries();
    $this->load->view('customer_add', $data);
}


public function get_countrieslistt() {
    $type = $this->input->get('type');
    $this->load->model('EnquiryModel');
    $countries = $this->EnquiryModel->get_countrieslistupdate($type);
    echo json_encode($countries);
}

// public function get_cities_by_countryy() {
//     $country_id = $this->input->post('country_id');
//     $this->load->model('EnquiryModel');
//     $cities = $this->EnquiryModel->get_cities_by_countryupdate($country_id);
//     echo json_encode($cities);
// }

public function get_cities_by_countryy() {
    $country_ids = $this->input->post('country_ids'); // array expected
    $this->load->model('EnquiryModel');
    $cities = $this->EnquiryModel->get_cities_by_countryupdate($country_ids);
    echo json_encode($cities);
}



// public function get_cities_by_country()
// {
//     $country_ids = $this->input->post('country_ids');
//     $this->load->model('EnquiryModel');
//     $cities = $this->EnquiryModel->get_cities_by_country($country_ids);
//     echo json_encode($cities);
// }

public function get_cities_by_country()
{
    $country_ids = $this->input->post('country_ids');
   
    if (!is_array($country_ids)) {
        $country_ids = [$country_ids];
    }
    
    $this->load->model('EnquiryModel');
    $cities = $this->EnquiryModel->get_cities_by_country($country_ids);
    echo json_encode($cities);
}

// public function get_cities_by_country()
// {
//     $country_ids = $this->input->post('country_ids');
//     if (!is_array($country_ids)) {
//         $country_ids = explode(',', $country_ids);
//     }

//     $this->load->model('EnquiryModel');
//     $cities = $this->EnquiryModel->get_cities_by_country($country_ids);
//     echo json_encode($cities);
// }




public function get_countrieslist()
    {
        $type = $this->input->get('type');   
        $this->load->model('EnquiryModel');
        $countries = $this->EnquiryModel->get_countries_by_type($type);
        echo json_encode($countries);
    }

//     public function get_cities_by_country()
// {
//     $country_ids = $this->input->post('country_ids');

//     // Load model
//     $this->load->model('EnquiryModel');
//     $cities = $this->EnquiryModel->get_cities_by_country($country_ids);

//     echo json_encode($cities);
// }


    // public function get_cities_by_country()
    // {
    //     $cid = $this->input->post('country_id');
    //     // $cid  = implode(',', $country_id);
    //     $this->load->model('EnquiryModel');
    //     $cities = $this->EnquiryModel->get_cities_by_country($cid);
        
    //     echo json_encode($cities);
    // } 

    // public function get_cities_by_country() {
    //     $country_id = $this->input->post('country_id');
        
    //     $country = $this->db->get_where('crcountry', ['country_id' => $country_id])->row();
        
    //     if ($country) {
    //         $this->db->where('country_name', $country->country_name);
    //         $cities = $this->db->get('crcity')->result_array();
    
    //         // Map country_name to act as city_name (temporary fix)
    //         $response = array_map(function($city) {
    //             return [
    //                 'city_id' => $city['city_id'],
    //                 'city_name' => $city['country_name'] // fake city_name
    //             ];
    //         }, $cities);
    
    //         echo json_encode($response);
    //     } else {
    //         echo json_encode([]);
    //     }
    // }


}
?>