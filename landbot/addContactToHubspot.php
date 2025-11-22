<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: *");
// extract($_REQUEST);
// $data=file_get_contents('php://input');
// $data='{
//   "properties": {
//     "email": "swapnil91991@gmail.com",
//     "firstname": "Test",
//     "lastname": "Bot",
//     "phone": "919881589589",
//     "which_country_are_you_from___landbot_": "Spain",
//     "what_are_you_interested_in": "Ielts",
//     "why_do_you_want_to_study_english": "University",
//     "when_would_you_like_to_start_your_course_": "2024-01-15",
//     "how_long_would_you_like_to_study_for": "2 weeks",
//     "needsaccommodation": "No",
//     "lifecyclestage": "marketingqualifiedlead",
//     "where_are_you_from": "Yes"
//   }
// }';
// $datas=json_decode($data,true)['properties'];

// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/contacts/".$datas['email']."?idProperty=email",
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'GET',
//   CURLOPT_HTTPHEADER => array(
//     'Content-Type: application/json',
//       'Authorization: Bearer ' . getenv('HUBSPOT_API_KEY')
//   ),
// ));
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
// $response = curl_exec($curl);
// $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// curl_close($curl);

// if($http_status=='200')
// {
//   curl_setopt_array($curl, array(
//     CURLOPT_URL => "https://api.hubapi.com/crm/v3/objects/contacts/".$datas['email']."?idProperty=email",
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'PATCH',
//     CURLOPT_POSTFIELDS =>$data,
//     CURLOPT_HTTPHEADER => array(
//       'Content-Type: application/json',
//       'Authorization: Bearer ' . getenv('HUBSPOT_API_KEY')
//     ),
//   ));
//   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//   $response = curl_exec($curl);
//   $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//   curl_error($curl);
//   curl_close($curl);
  
// }
// else
// {
//   $curl = curl_init();

//   curl_setopt_array($curl, array(
//     CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/',
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'POST',
//     CURLOPT_POSTFIELDS =>$data,
//     CURLOPT_HTTPHEADER => array(
//       'Content-Type: application/json',
//     'Authorization: Bearer ' . getenv('HUBSPOT_API_KEY')
//     ),
//   ));
//   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
//   $response = curl_exec($curl);
//   // echo curl_error($curl);
//   curl_close($curl);
//}

// '{
//     "collectionName":"newenquiries",
//     "email":"'.$data['email'].'",
//     "name":"'.$data['firstname'].'",
//     "lastname":"'.$data['lastname'].'",
//     "phone":"'.$data['phone'].'",
//     "country":"'.$data['which_country_are_you_from___landbot_'].'",
//     "course":"'.$data['what_are_you_interested_in'].'",
//     "goal":"'.$data['why_do_you_want_to_study_english'].'",
//     "course_start":"'.$data['when_would_you_like_to_start_your_course_'].'", 
//     "course_duration":"'.$data['how_long_would_you_like_to_study_for'].'",
//     "accommodation":"'.$data['needsaccommodation'].'",
//     "location":"'.$data['where_are_you_from'].'",
//   }'
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://globaltourmanager.com:5005/enquiry-web',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
"collectionName": "newenquiries",
"email": "swapnil91991@gmail.com"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
 echo $response = curl_exec($curl);
echo curl_error($curl);
  curl_close($curl);
  // $response=json_decode($response,true);
  // print_r($response);
  // if($response['success']==true)
  // {
  //   curl_setopt_array($curl, array(
  //     CURLOPT_URL => 'http://globaltourmanager.com:5005/send-email',
  //     CURLOPT_RETURNTRANSFER => true,
  //     CURLOPT_ENCODING => '',
  //     CURLOPT_MAXREDIRS => 10,
  //     CURLOPT_TIMEOUT => 0,
  //     CURLOPT_FOLLOWLOCATION => true,
  //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //     CURLOPT_CUSTOMREQUEST => 'POST',
  //     CURLOPT_POSTFIELDS =>'{
  //     "to": "swapnil919912gmail.com",
  //     "subject": "Welcome to SpeakUpLondon School",
  //     "body": "Hi,<br><br>Your Email id is swapnil919912gmail.com And Password is swapnil919912gmail.com " 
  //   }',
  //     CURLOPT_HTTPHEADER => array(
  //       'Content-Type: application/json'
  //     ),
  //   ));
  //   curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  //   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
  // }
  // echo $response = curl_exec($curl);

  // curl_close($curl);
