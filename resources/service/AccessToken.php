<?php

class AccessToken
{
    public static $refreshParams = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_HEADER => 1
    ];

    public static $params = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_POST => 1
    ];

    /**
     * Returns an Access Token & Refresh Token
     * 
     * @param bool $justToken if TRUE then only the access_token is returned
     * @param string|null $refresh if supplied then it will refresh instead of calling a new auth code#
     * 
     * @return object|string will return object containing refresh token and access token unless told to just return token
     */
    public static function get(bool $justToken, string $refresh = null)
    {
        $code = $refresh ?? AuthCode::get();
        $type = $refresh == null ? 'authorization_code&code' : 'refresh_token&refresh_token';
        $config = parse_ini_file('config.ini');
        $client = $config['CLIENT_ID'];
        $secret = $config['CLIENT_SECRET'];
        $url = $config['OAUTH_URL'];

        $ch = curl_init("$url/token?grant_type=$type=$code&client_id=$client&client_secret=$secret");
        curl_setopt_array($ch, AccessToken::$params);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::fatal(curl_error($ch));
            die('rats... ' . curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($response, true);

        Log::info("AccessCode Created: ".$response['access_token']);

        if ($justToken)
            return $response['access_token'];

        return $response;
    }
}
