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

$json = [
    'authCode' => $AUTH_CODE,
    'accessToken' =>$ACTIVE_TOKENS->access_token,
    'refreshToken' => $ACTIVE_TOKENS->refresh_token,
    'restToken' => $REST_DATA->BhRestToken,
    'restUrl' => $REST_DATA->restUrl
];

die(json_encode($json));

// echo "
// Auth Token: $AUTH_CODE<br>
// Access Token: " . $ACTIVE_TOKENS->access_token . "<br>
// Refresh Token: " . $ACTIVE_TOKENS->refresh_token . "<br>
// BhRestToken: " . $REST_DATA->BhRestToken . "<br>
// Rest URL: " . $REST_DATA->restUrl;

// function getAuthorizationEndpoint()
// {
//     return new
//     Uri('https://auth.bullhornstaffing.com/oauth/authorize');}

// function getAccessTokenEndpoint()
// {
//     return new Uri('https://auth.bullhornstaffing.com/oauth/token');
// }

// function getLoginEndpoint()
// {
//     return new
//     Uri('https://rest.bullhornstaffing.com/rest-services/login');
// }

// //Once I have the code from the login, I call this:
// function getAccessTokenWithCodeUri($code, array $additionalParameters = array()) {
//     $parameters = array_merge(
//         $additionalParameters,
//         array(
//             'code' => $code,
//             'client_id' => $this->credentials->getConsumerId(),
//             'client_secret' =>
//             $this->credentials->getConsumerSecret(), 'redirect_uri' =>
//             'http://www.bullhorn.com', 'grant_type' => 'authorization_code',
//         )
//     );

//     // Build the url
//     $url = clone $this->getAccessTokenEndpoint();
//     foreach ($parameters as $key => $val) {
//         $url->addToQuery($key, $val);
//     }

//     return $url;
// }

// https://rest.bullhornstaffing.com/rest-services/login?version=2.0&access_token={xxxxxxxx}
