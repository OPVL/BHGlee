<?php
require "../resources/service/functions.php";

static $params = [
    CURLOPT_FOLLOWLOCATION => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_MAXREDIRS => 0,
    CURLOPT_POST => 1
];
$ch = curl_init($_COOKIE['restUrl'] . "logout?BhRestToken=" . $_GET['BhRestToken']);
curl_setopt_array($ch, $params);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    Log::fatal(curl_error($ch));
    die('rats... ' . curl_error($ch));
}
curl_close($ch);
$response = json_decode($response, true);
if (!$response['logout']) {
    Log::fatal("failed to log out: " . $response['errorMessage']);
}
Log::info("User logged out: " . $_COOKIE['BhRestToken']);

setcookie("BhRestToken", "", 0, '/');
setcookie("refresh_token", "", 0, '/');
setcookie("restUrl", "", 0, '/');
setcookie("userId", "", 0, '/');

header("Location: /gleesons/login");
