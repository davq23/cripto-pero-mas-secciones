<?php

namespace controllers;

class EmailController extends Controller {

    public function send() {
        $requestBody = file_get_contents('php://input');

        header('Content-Type: application/json');

        $requestArray = json_decode($requestBody, true);

        if (!$requestArray) {
            http_response_code(400);

            $result = [
                'message' => 'Invalid payload',
            ];
            
            exit();
        } else {
        $subjectEmail = $requestArray['subject'];
        $messageEmail = $requestArray['message'];
        $fromEmail = $requestArray['from'];
        
        if (empty($subjectEmail) || empty($messageEmail) || empty($fromEmail)) {
            http_response_code(400);
           
            exit();
        }
        
        
        }
        $cabeceras = "From: $fromEmail" . "\r\n";
        
        $sent = mail(getenv('email'), $subjectEmail, $messageEmail, $cabeceras);
        
        if (!$sent) {
            http_response_code(400);
        
            exit();
        }
    }

}