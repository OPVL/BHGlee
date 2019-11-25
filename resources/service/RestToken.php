<?php

use App\Service\Log;
use App\Service\Bullhorn\AccessToken;

namespace App\Service\Bullhorn;

class RestToken
{
    public static $params = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_POST => 1
    ];

    /**
     * Returns BhRestToken & RestUrl
     * 
     * @param string $token Access Token - AccessToken::get()
     * @param string $version specify what version of the API you want or use the latest
     * 
     * @return array associative array containing the RestToken and RestUrl
     */
    public static function get(bool $justToken, $token = null, $version = '*')
    {
        $access = $token ?? AccessToken::get(true);
        $url = "https://rest.bullhornstaffing.com/rest-services/login?version=$version&access_token=$access";
        $ch = curl_init($url);
        curl_setopt_array($ch, RestToken::$params);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::fatal(curl_error($ch));
            die('rats... ' . curl_error($ch));
        }

        curl_close($ch);
        $response = json_decode($response, true);

        Log::info("AccessCode Created: ".$response['BhRestToken']);

        if ($justToken)
            return $response['BhRestToken'];

        return $response;
    }
}