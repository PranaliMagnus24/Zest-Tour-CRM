<?php
// error_reporting(0);

function agent()
{    
    extract($_REQUEST);
  $headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . getenv('HUBSPOT_API_KEY'),
  ];
  $land_headers = [
      'Content-Type: application/json',
      'Authorization: Token  d6c24e3a9459a1aa1ebd29e46ccf601dd1598863',
  ];

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_URL, "https://api.hubapi.com/crm/v3/objects/contacts/$email?idProperty=email&properties=hubspot_owner_id,hubspot_owner_assigneddate,hubspot_team_id");
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  // curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $contacts = curl_exec($curl);
  curl_error($curl);
  curl_close($curl);
  $contacts=json_decode($contacts,true);
  $hubspot_owner_id= $contacts['properties']['hubspot_owner_id'];
  if($hubspot_owner_id!='' && $hubspot_owner_id!=null)
  {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_URL, 'https://api.hubapi.com/crm/v3/owners/'.$hubspot_owner_id);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      // curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $contacts = curl_exec($curl);
      // echo curl_error($curl);
      curl_close($curl);
      $contacts=json_decode($contacts,true);
      $agent_email= $contacts['email'];




      // //https://api.landbot.io/v1/customers/405553318/assign/576725/
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_HTTPHEADER, $land_headers);
      curl_setopt($curl, CURLOPT_URL, "https://api.landbot.io/v1/agents/");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      // curl_setopt($curl, CURLOPT_PUT, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $contacts = curl_exec($curl);
      echo curl_error($curl);
      curl_close($curl);
      $contacts=json_decode($contacts,true);
      foreach($contacts['agents'] as $key=>$value)
      {
          if($agent_email==$value['email'])
          {
              $land_agent_id=$value['id'];
          }
      }
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_HTTPHEADER, $land_headers);
      curl_setopt($curl, CURLOPT_URL, "https://api.landbot.io/v1/customers/?search=$email&limit=1");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      // curl_setopt($curl, CURLOPT_PUT, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $contacts = curl_exec($curl);
      echo curl_error($curl);
      curl_close($curl);

      $contacts=json_decode($contacts,true);

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_HTTPHEADER, $land_headers);
      curl_setopt($curl, CURLOPT_URL, 'https://api.landbot.io/v1/customers/'.$contacts['customers'][0]['id'].'/assign/'.$land_agent_id.'/');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

      curl_setopt($curl, CURLOPT_PUT, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $contacts = curl_exec($curl);
      echo curl_error($curl);
      curl_close($curl);

      echo ($contacts);

  }
  else
  {
      // $land_agent_id=610160;
      agent();
  }    
}
agent();
?>