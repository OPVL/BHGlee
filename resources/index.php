<?php

require "service/functions.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $tokens = RestToken::get(false, [$_POST['username'], $_POST['password']]);
    // $tokens = RestToken::get(false);

    if (!$tokens['BhRestToken']) {
        header("Location: /gleesons/login?status=danger&message=1");
        return;
    }

    setcookie("BhRestToken", $tokens['BhRestToken'], time() + $tokens['expires_in'], '/');
    setcookie("refresh_token", $tokens['refresh_token'], time() + 60 * 60 * 24 * 30, '/');
    setcookie("restUrl", $tokens['restUrl'], time() + 60 * 60 * 24 * 30, '/');

    header("Location: /gleesons/" . ($_POST['origin'] . $_POST['term']) ?? 'dashboard');
}

if (isset($_GET['refresh'])) {
    $tokens = RestToken::refresh(false, $_GET['refresh'] ?? $_COOKIE['refresh_token']);

    if (!$tokens['BhRestToken']) {
        setcookie("BhRestToken", "", 0, '/');
        setcookie("refresh_token", "", 0, '/');
        die('invalid');
        return json_encode(tokens);
    }

    setcookie("BhRestToken", $tokens['BhRestToken'], time() + $tokens['expires_in'], '/');
    setcookie("refresh_token", $tokens['refresh_token'], time() + 60 * 60 * 24 * 30, '/');
    setcookie("restUrl", $tokens['restUrl'], time() + 60 * 60 * 24 * 30, '/');

    die(json_encode($tokens));
}