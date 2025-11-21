<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notification extends CI_model
{
    function send_notification($token='',$messages='',$call='')
    {
        define( 'FIREBASE_API_KEY', 'AAAAHhOSOUo:APA91bFWZuBqaCHB2SSyjfMOZwh-a8UiOeGzcKSGODyIhFDnTP3xByVQmYOWSAs34be8xBZIGfQw1PBb8CgjQ4N9tP2x4BA_cEbVabuB1KyT33MbO_2_3o6fHhsNtRqZEC-cXnp4aD7e' );
        $msg = array (
            'msg'     => $messages,
            'weblink'   => $call,
            'item_id' => "1",										
        );

        $fields = array (
            'to' => $token,
            'data' => array('message' => $msg)
        );

        $headers = array (
            'Authorization: key=' . FIREBASE_API_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        "Result: ". $result;
    }

}