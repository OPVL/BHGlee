<?php

function test()
{
    $ch = curl_init("https://auth.bullhornstaffing.com/oauth/authorize?client_id=3b2ab272-af50-4098-a3b0-f8fe712c01e1&response_type=code");

    $params = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_HEADER => 1
    ];
    curl_setopt_array($ch, $params);

    $response = curl_exec($ch);

    if (curl_errno( $ch)){
        die(curl_error($ch));
    }

    die($response);
}

class ClientLogin {
    public static function login(string $username, string $password){
        
    }
}

test();