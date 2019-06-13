<?php

require "functions.php";

$request = $_GET['type'] ?? die(RestToken::get($_GET['justToken'] ?? false));