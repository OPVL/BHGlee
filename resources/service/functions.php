<?php
define('OAUTH_URL', 'https://auth.bullhornstaffing.com/oauth');

function getAuthToken($client, $user, $pass)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, OAUTH_URL . "/authorize?client_id=$client&response_type=code&username=$user&password=$pass&action=Login");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    if (preg_match("|Location: (https?://\S+)|", $response, $m)) {
        //Location is in $m[1]
        if (preg_match("|code=(\S+)\&client_id|", $m[1], $n)) {
            return urldecode($n[1]);
        }
    }
}

function getAccessToken($auth, $client, $secret)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, OAUTH_URL . "/token?grant_type=authorization_code&code=$auth&client_id=$client&client_secret=$secret");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
    // curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);

    curl_close($ch);

    return json_decode($response);
}

function getRestToken($access, $version = '*')
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://rest.bullhornstaffing.com/rest-services/login?version=$version&access_token=$access");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return json_decode($response);
}
