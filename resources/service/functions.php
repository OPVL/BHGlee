<?php
define('OAUTH_URL', 'https://auth.bullhornstaffing.com/oauth');

class Token
{
    private $_expiry = 600; // expiry time for UNIX epoch, default is 10 minutes
    public $table = 'access_tokens'; // the table that it looks for

    private $_conn;

    protected $id = 1;
    private $_token;
    private $_created_at;
    private $_expired = false;

    public function __construct(int $id = null, string $table, int $expiry)
    {
        $this->table = $table;
        $this->expiry = $expiry;
        $this->_conn = connect();
        $this->id = $id ?? $this->newest($this->_conn);
        $this->$_expired = $this->get();
    }

    private function isValid()
    {
        $this->_expired = (time() - $this->_created_at < $this->_expiry);

        if ($this->_expired) {
            $sql = "UPDATE " . $this->table . " expired=1 WHERE id=" . $this->id;
            if ($this->_conn->query($sql) === false) {
                die(Token::jsonError($this->_conn->error));
            }
            return false;
        }
        return true;
    }

    private function get()
    {
        $sql = "SELECT * FROM " . $this->table . "WHERE id=" . $this->id . " ORDER BY created_at DESC LIMIT 1";

        $res = $this->_conn->query($sql);

        if (!$res) {
            echo (jsonError($this->_conn->error));
            return null;
        }

        $row = $res->fetch_assoc();

        $this->_created_at = $row['created_at'];
        $this->_token = $row['token'];

        return $row['expired'] ? false : true;
    }

    private function newest($conn)
    {
        $sql = "SELECT id FROM $table ORDER BY id DESC LIMIT 1";
        $res = $conn->query($sql);
        if ($res !== false) {
            $row = $res->fetch_assoc();
            return $row['id'];
        }
    }

    public static function jsonError(String $msg)
    {
        $json = [
            'error' => $msg,
        ];
        return (json_encode($json));
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
    private $_url;
    protected $code;

    public function __construct()
    {
        try {
            $config = parse_ini_file('config.ini');
            $this->_url = $this->buildUrl($config);
        } catch (Exception $e) {
            echo $e;
        }
    }

    private function buildUrl(array $config)
    {
        try {
            $client = $config['CLIENT_ID'];
            $username = $config['USERNAME'];
            $password = $config['PASSWORD'];
            return OAUTH_URL.$this->url = "/authorize?client_id=$client&response_type=code&username=$username&password=$password&action=Login";
        } catch (Exception $e) {
            echo $e;
            return null;
        }
    }

    public function get()
    {
        try {
            $ch = curl_init($this->_url);
            curl_setopt_array($ch, AuthCode::$params);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                return 'Error:' . curl_error($ch);
            }

            curl_close($ch);

            if (preg_match("|Location: (https?://\S+)|", $response, $m)) {
                //Location is in $m[1]
                if (preg_match("|code=(\S+)\&client_id|", $m[1], $n)) {
                    $code = urldecode($n[1]);
                    return $code;
                }
            }
            return null;
        } catch (Exception $e) {
            echo $e;
        }
    }
}

class AccessToken extends Token
{
    private $_refresh;

    public function __construct($id = null)
    {
        parent::__construct($id, 'access_tokens', 600);
    }

    public function refresh($refresh, $client, $secret)
    {
        $sql = "SELECT id, token, created_at, refresh_token FROM access_tokens ORDER BY id DESC LIMIT 1";

        $url = OAUTH_URL . "/token?grant_type=refresh_token&refresh_token=$refresh&client_id=$client&client_secret=$secret";
    }

    public function getNew($conn, $auth, $client, $secret)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, OAUTH_URL . "/token?grant_type=authorization_code&code=$auth&client_id=$client&client_secret=$secret");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        curl_setopt($ch, CURLOPT_POST, true);

        $response = curl_exec($ch);

        curl_close($ch);
    }
}

function connect()
{
    try {
        $config = parse_ini_file('service/config.ini');
        $DB_HOST = $config['DB_HOST'];
        $DB_PORT = $config['DB_PORT'];
        $DB_USER = $config['DB_USER'];
        $DB_PASS = $config['DB_PASS'];
    } catch (\Throwable $th) {
        throw $th;
    }

    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, 'gleecall', 3306);
    // Check connection
    if ($conn->connect_error) {
        return jsonError("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function getAuthCode($client, $user, $pass)
{ }

function getNewAccessToken($refresh, $client, $secret)
{
    $sql = "SELECT id, token, created_at, refresh_token FROM access_tokens ORDER BY id DESC LIMIT 1";

    $url = OAUTH_URL . "/token?grant_type=refresh_token&refresh_token=$refresh&client_id=$client&client_secret=$secret";
}

function getAccessToken($auth, $client, $secret)
{
    $db = connect();
    if (!$db) {
        die(jsonError($db->connect_error));
    }

    $token = new AccessToken($db);
    //return $token->getToken($conn) ?? $token-> ;

    $sql = "SELECT id FROM access_tokens ORDER BY id DESC LIMIT 1";
    $res = $db->query($sql);
    try {
        //code...
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $token = new AccessToken($row['id']);

                // access tokens are only valid for 10 minutes, if our saved access_token is still valid then we can use it to get BhRestTokens
                if (time() - $row['created_at'] < 600 && !$row['expired']) {
                    continue;
                    //return($row['code']);
                }
                $sql = "UPDATE access_tokens expired=1 WHERE id=" . $row['id'];
                if ($conn->query($sql) === false) {
                    die(jsonError($db->error));
                }
            }
        }
    } catch (\Throwable $th) {
        die($th);
    }

    $sql = "INSERT INTO access_tokens (code, created_at, creation_ip) VALUES ('$code', UNIX_TIMESTAMP()," . $_SERVER['REMOTE_ADDR'] . ")";

    if ($db->query($sql) === false) {
        $json = ['error' => "Error: $sql " . $conn->error];
        die(json_encode($json));
    }

    return json_decode($response);
}

function getRestToken($access, $version = '*')
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://rest.bullhornstaffing.com/rest-services/login?version=$version&access_token=$access");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
    curl_setopt($ch, CURLOPT_POST, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return json_decode($response);
}
