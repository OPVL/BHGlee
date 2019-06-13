<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DatabaseMisc
{
    public static function getConfig()
    {
        try {
            $config = parse_ini_file('config.ini');
        } catch (Exception $e) {
            throw $e->getMessage();
        }
        return [
            'host' => $config['DB_HOST'],
            'port' => $config['DB_PORT'],
            'user' => $config['DB_USER'],
            'password' => $config['DB_PASS']
        ];
    }
    public static function jsonError(string $msg)
    {
        $json = [
            'error' => $msg,
        ];
        return (json_encode($json));
    }
    public static function connect()
    {
        $conf = DatabaseMisc::getConfig();

        $conn = new mysqli($conf['host'], $conf['user'], $conf['password'], 'gleecall', $conf['port']);
        // Check connection
        if ($conn->connect_error) {
            die(DatabaseMisc::jsonError("Connection failed: " . $conn->connect_error));
        }
        return $conn;
    }
}
class Log
{
    public static $table = 'event_log';

    /**
     * Logs events in to the database, has overload methods for default success, fail, info
     * @param string $msg   save data
     * @param int|enum $type INFO | SUCCESS | FAIL | FATAL
     * @return bool save ? TRUE : FALSE
     */
    public static function save($msg, $type = 'CUSTOM')
    {
        $conn = DatabaseMisc::connect();

        $columns['message'] = "'$msg'";
        $columns['category'] = "'$type'";
        $columns['time'] = time();
        $columns['ip'] = "'" . $_SERVER['REMOTE_ADDR'] . "'";

        $values = implode(',', array_values($columns));
        $columns = implode(',', array_keys($columns));

        $sql = "INSERT INTO " . Log::$table . " ( $columns ) VALUES ( $values )";

        if (!$conn->query($sql)) {
            die($conn->error);
        }

        return TRUE;
    }

    /**
     * logs a success event to the database
     * 
     * @param string $msg a description of the event
     * @return bool success or fail on save
     */
    public static function success($msg)
    {
        return Log::save($msg, 'SUCCESS');
    }

    /**
     * logs a failiure event to the database
     * 
     * @param string $msg a description of the event
     * @return bool success or fail on save
     */
    public static function fail($msg)
    {
        return Log::save($msg, 'FAIL');
    }

    /**
     * logs a fatal event to the database
     * 
     * @param string $msg a description of the event
     * @return bool success or fail on save
     */
    public static function fatal($msg)
    {
        return Log::save($msg, 'FATAL');
    }

    /**
     * logs an info event to the database
     * 
     * @param string $msg a description of the event
     * @return bool success or fail on save
     */
    public static function info($msg)
    {
        return Log::save($msg, 'INFO');
    }
}

class AuthCode
{
    static $params = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_HEADER => 1
    ];

    public static function buildUrl(array $config, array $credentials = null)
    {
        try {
            $client = $config['CLIENT_ID'];
            $username = $credentials[0] ?? $config['USERNAME'];
            $password = $credentials[1] ?? $config['PASSWORD'];
            $url = $config['OAUTH_URL'];

            return "$url/authorize?client_id=$client&response_type=code&username=$username&password=$password&action=Login";
        } catch (Exception $e) {
            echo $e;
            return null;
        }
    }

    /**
     * Returns the Auth Code which is then used to get the access code
     * 
     * @return string Auth Code
     */
    public static function get(array $credentials = null)
    {
        $url = AuthCode::buildUrl(parse_ini_file('config.ini'), $credentials);
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, AuthCode::$params);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                Log::fatal(curl_error($ch));
                die('rats... ' . curl_error($ch));
            }

            curl_close($ch);

            if (preg_match("|Location: (https?://\S+)|", $response, $m)) {
                //Location is in $m[1]
                if (preg_match("|code=(\S+)\&client_id|", $m[1], $n)) {
                    $code = urldecode($n[1]);
                    Log::info("AuthCode Created: $code");
                    return $code;
                }
            }
            return null;
        } catch (Exception $e) {
            Log::fatal($e);
        }
    }
}
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
     * @param string|null $authCode if supplied then will get an access_token and refresh_token from specified authCode
     * @param string|null $refresh if supplied then it will refresh instead of calling a new auth code#
     * 
     * @return object|string will return object containing refresh token and access token unless told to just return token
     */
    public static function get(bool $justToken, string $authCode = null, string $refresh = null)
    {
        $code = $refresh ?? $authCode ?? AuthCode::get();
        if (!$code)
            return null;
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

        Log::info("AccessCode C r eated: " . $response['access_token']);

        if ($justToken)
            return $response['access_token'];

        return $response;
    }
}
class RestToken
{
    public static $params = [
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_MAXREDIRS => 0,
        CURLOPT_POST => 1
    ];

    /**
     * Returns BhRestToken & RestUrl
     * 
     * @param string $token Access Token - AccessToken::get()
     * @param string $version specify what version of the API you want or use the latest
     * 
     * @return array associative array containing the RestToken and RestUrl
     */
    public static function get(bool $justToken, array $credentials = null)
    {
        $auth = AuthCode::get($credentials);
        $access = AccessToken::get(false, $auth, null);

        if (!$auth || !$access) {
            return [];
        }
        // return NULL;
        $url = "https://rest.bullhornstaffing.com/rest-services/login?version=*&access_token=" . $access['access_token'];
        $ch = curl_init($url);
        curl_setopt_array($ch, RestToken::$params);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::fatal(curl_error($ch));
            die('rats... ' . curl_error($ch));
        }

        curl_close($ch);
        $response = json_decode($response, true);

        Log::info("RestToken Created: " . $response['BhRestToken']);

        if ($justToken)
            return json_encode("{ 'token':'" . $response['BhRestToken'] . "' }");

        $response = array_merge($response, $access);

        return $response;
    }

    public static function refresh(bool $justToken, string $refresh)
    {
        $access = AccessToken::get($justToken, null, $refresh);

        if (!$access) {
            return [];
        }
        // return NULL;
        $url = "https://rest.bullhornstaffing.com/rest-services/login?version=*&access_token=" . $access['access_token'];
        $ch = curl_init($url);
        curl_setopt_array($ch, RestToken::$params);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::fatal(curl_error($ch));
            die('rats... ' . curl_error($ch));
        }

        curl_close($ch);
        $response = json_decode($response, true);

        Log::info("RestToken Created: " . $response['BhRestToken']);

        if ($justToken)
            return json_encode("{ 'token':'" . $response['BhRestToken'] . "' }");

        $response = array_merge($response, $access);

        return $response;
    }
}

// print_r(RestToken::get(false, ['training.gle','Disco2019!']));

// print_r(RestToken::get(false));

// Array ( 
//     [access_token] => 23:7a437f92-b134-433c-8ab5-d5ac40940189 
//     [token_type] => Bearer 
//     [expires_in] => 600 
//     [refresh_token] => 23:1a8517d4-a6a0-4a8f-94b2-3ce4a69b56b4 
//     ) 
// Array ( 
//     [BhRestToken] => a7529ba2-9228-42cf-b6e8-3943a9691838 
//     [restUrl] => https://rest23.bullhornstaffing.com/rest-services/3rn5us/ 
//     ) 
    
//     {
//         "BhRestToken":"a7529ba2-9228-42cf-b6e8-3943a9691838",
//         "restUrl":"https:\/\/rest23.bullhornstaffing.com\/rest-services\/3rn5us\/",
//         "access_token":"23:7a437f92-b134-433c-8ab5-d5ac40940189",
//         "token_type":"Bearer",
//         "expires_in":600,
//         "refresh_token":"23:1a8517d4-a6a0-4a8f-94b2-3ce4a69b56b4"
//     }
