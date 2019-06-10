<?php
require "service/config.php";
require "service/functions.php";

$REFRESH_TOKEN = null;
$AUTH_CODE = null;

if (isset($_GET['refresh'])) {
    $REFRESH_TOKEN = $_GET['refresh'];
} else {
    if (isset($_GET['auth_code'])) {
        $AUTH_CODE = $_GET['auth_code'];
    } else {
        // Login and get Auth Code so we can get refresh token and stpo the requirement to login with each request.
        $AUTH_CODE = getAuthToken([$client_id, $username, $password]);
        // $AUTH_CODE;
    }
    $REFRESH_TOKEN = getRefreshToken();
}

echo getAuthCode();

// https://rest.bullhornstaffing.com/rest-services/login?version=2.0&access_token={xxxxxxxx}
