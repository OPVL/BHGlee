<?php
require "service/functions.php";
require "service/config.php";

$ACTIVE_TOKENS = [];
$AUTH_CODE = null;

if (isset($_GET['refresh'])) {
    $REFRESH_TOKEN = $_GET['refresh'];
} else {
    if (isset($_GET['auth_code'])) {
        $AUTH_CODE = $_GET['auth_code'];
    } else {
        // Login and get Auth Code so we can get refresh token and stop the requirement to login with each request.
        $AUTH_CODE = getAuthToken($CLIENT_ID, $USERNAME, $PASSWORD);
    }
    $ACTIVE_TOKENS = getAccessToken($AUTH_CODE, $CLIENT_ID, $CLIENT_SECRET);
}

$REST_DATA = getRestToken($ACTIVE_TOKENS->access_token);

echo "
Auth Token: $AUTH_CODE<br>
Access Token: " . $ACTIVE_TOKENS->access_token . "<br>
Refresh Token: " . $ACTIVE_TOKENS->refresh_token . "<br>
BhRestToken: " . $REST_DATA->BhRestToken . "<br>
Rest URL: " . $REST_DATA->restUrl;

// https://rest.bullhornstaffing.com/rest-services/login?version=2.0&access_token={xxxxxxxx}
