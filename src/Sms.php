<?php
namespace Simcify;

use AfricasTalking\SDK\AfricasTalking;
use Twilio\Rest\Client;

class Sms {

    /**
     * Send SMS With Africa's Talking
     * 
     * @param   string $phone number
     * @param   string $message
     * @return  array
     */
    public static function africastalking($phoneNumber,$message) {
        $username = env('AFRICASTALKING_USERNAME'); 
        $apiKey   = env('AFRICASTALKING_KEY'); 
        $from   = env('AFRICASTALKING_SENDERID');
        $AT       = new AfricasTalking($username, $apiKey);
        
        // Get one of the services
        $sms      = $AT->sms();
        
        if(empty($from)){
           // Use the service
            $response   = $sms->send([
                'to'      => $phoneNumber,
                'message' => $message
            ]); 
        }else{
            // Use the service
            $response   = $sms->send([
                'to'      => $phoneNumber,
                'message' => $message,
                'from' => $from
            ]);
        }

        if ($response["status"] == "success") {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Send SMS With Twilio
     * 
     * @param   string $phone number
     * @param   string $message
     * @return  array
     */
    public static function twilio($phoneNumber,$message) {

        $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTHTOKEN'));
        try{ 
            $response = $client->account->messages->create(
                $phoneNumber,
                array(
                    'from' => env('TWILIO_PHONENUMBER'), 
                    'body' => $message
                )
            );
          }catch(\Exception $e){  
            return false;
          }

        if ($response->status == "sent" || $response->status == "queued") {
            return true;
        }else{
            return false;
        }
    }
}

