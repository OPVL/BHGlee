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

    public static function buildUrl(array $config)
    {
        try {
            $client = $config['CLIENT_ID'];
            $username = $config['USERNAME'];
            $password = $config['PASSWORD'];
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
    public static function get()
    {
        $url = AuthCode::buildUrl(parse_ini_file('config.ini'));
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
            return FALSE;
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
     * @param string|null $refresh if supplied then it will refresh instead of calling a new auth code#
     * 
     * @return object|string will return object containing refresh token and access token unless told to just return token
     */
    public static function get(bool $justToken, string $refresh = null)
    {
        $code = $refresh ?? AuthCode::get();
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

        $response = json_decode($response);

        Log::info("AccessCode Created: $response->access_token");

        if ($justToken)
            return $response->access_token;

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
    public static function get(bool $justToken, $token = null, $version = '*')
    {
        $access = $token ?? AccessToken::get(true);
        $url = "https://rest.bullhornstaffing.com/rest-services/login?version=$version&access_token=$access";
        $ch = curl_init($url);
        curl_setopt_array($ch, RestToken::$params);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::fatal(curl_error($ch));
            die('rats... ' . curl_error($ch));
        }

        curl_close($ch);
        $response = json_decode($response, true);

        Log::info("AccessCode Created: ".$response['BhRestToken']);

        if ($justToken)
            return $response['BhRestToken'];

        return $response;
    }
}

print_r("AuthCode: ".AuthCode::get());
print_r("<br>AccessToken: ".AccessToken::get(true));
print_r("<br>RestToken: ".RestToken::get(true));