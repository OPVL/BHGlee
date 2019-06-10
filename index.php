<?php
require "service/functions.php";
require "service/config.php";

$REFRESH_TOKEN = null;
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
    $REFRESH_TOKEN = getRefreshToken($AUTH_CODE, $CLIENT_ID, $CLIENT_SECRET);
}

echo "Auth Token: $AUTH_CODE<br>Refresh: $REFRESH_TOKEN";

// https://rest.bullhornstaffing.com/rest-services/login?version=2.0&access_token={xxxxxxxx}
