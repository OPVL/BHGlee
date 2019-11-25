<?php
// phpinfo();
// if (! function_exists ( 'curl_version' )) {
//     exit ( "Enable cURL in PHP" );
// }

// $ch = curl_init ();
// $timeout = 0; // 100; // set to zero for no timeout
// $myHITurl = "http://www.google.com";
// curl_setopt ( $ch, CURLOPT_URL, $myHITurl );
// curl_setopt ( $ch, CURLOPT_HEADER, 0 );
// curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
// curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
// $file_contents = curl_exec ( $ch );
// if (curl_errno ( $ch )) {
//     echo curl_error ( $ch );
//     curl_close ( $ch );
//     exit ();
// }
// curl_close ( $ch );

// // dump output of api if you want during test
// echo "$file_contents";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ch = curl_init("https://auth.bullhornstaffing.com/oauth/token?grant_type=authorization_code&code=23:5a11ae73-09a2-4a0d-b3fe-14b682320400&client_id=3b2ab272-af50-4098-a3b0-f8fe712c01e1&client_secret=bne5ncAwEMNVwbCfJ0EGq2HU");
// curl_setopt_array($ch, [
//     CURLOPT_FOLLOWLOCATION => 0,
//     CURLOPT_RETURNTRANSFER => 1,
//     CURLOPT_MAXREDIRS => 0,
//     CURLOPT_POST => 1
// ]);
$code = "23:aa38c1ea-7fc0-4288-9a7c-9cb17a6078f7";
curl_setopt($ch, CURLOPT_URL, "https://auth.bullhornstaffing.com/oauth/token?grant_type=authorization_code&code=$code&client_id=3b2ab272-af50-4098-a3b0-f8fe712c01e1&client_secret=bne5ncAwEMNVwbCfJ0EGq2HU");
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
curl_setopt($ch, CURLOPT_POST, 1);
$response = curl_exec($ch);

if ($response == null){
    die("why the fuck are you null?");
}

// die(json_decode($response));
if (curl_errno($ch)) {
    die('rats... ' . curl_error($ch));
}

die($response);
curl_close($ch);

$response = json_decode($response, true);
