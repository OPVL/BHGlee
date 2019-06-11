<?php
define('OAUTH_URL', 'https://auth.bullhornstaffing.com/oauth');

class DatabaseMisc
{
    public static function jsonError(String $msg)
    {
        $json = [
            'error' => $msg,
        ];
        return (json_encode($json));
    }
    public static function connect()
    {
        try {
            $config = parse_ini_file('config.ini');
            $DB_HOST = $config['DB_HOST'];
            $DB_PORT = $config['DB_PORT'];
            $DB_USER = $config['DB_USER'];
            $DB_PASS = $config['DB_PASS'];
        } catch (Exception $e) {
            throw $e->getMessage();
        }

        $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, 'gleecall', 3306);
        // Check connection
        if ($conn->connect_error) {
            die(DatabaseMisc::jsonError("Connection failed: " . $conn->connect_error));
        }
        return $conn;
    }
}

class Token
{
    private $_expiry = 600; // expiry time for UNIX epoch, default is 10 minutes
    public $table = 'access_tokens'; // the table that it looks for

    private $_conn;

    protected $id = 0;
    private $_token;
    private $_created_at;
    private $_expired = FALSE;
    protected $_exists = FALSE;

    public function __construct(int $id = null, string $table, int $expiry)
    {
        $this->table = $table;
        $this->expiry = $expiry;
        $this->_conn = DatabaseMisc::connect();

        $this->id = $id ?? $this->newest();
        if (!$this->id) {
            return;
        }
        $this->_exists = TRUE;
        $this->$_expired = $this->get();
    }

    public function isValid()
    {
        $this->_expired = (time() - $this->_created_at < $this->_expiry);

        if ($this->_expired) {
            $sql = "UPDATE " . $this->table . " expired=1 WHERE id=" . $this->id;
            if ($this->_conn->query($sql) === FALSE) {
                die(DatabaseMisc::jsonError($this->_conn->error));
            }
            return FALSE;
        }
        return TRUE;
    }

    public function get()
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=" . $this->id . " ORDER BY created_at DESC LIMIT 1";

        $res = $this->_conn->query($sql);

        if (!$res) {
            die(DatabaseMisc::jsonError($this->_conn->error));
        }

        if ($res->num_rows <= 0)
            return FALSE;

        $row = $res->fetch_assoc();

        $this->_created_at = $row['created_at'];
        $this->_token = $row['token'];
        $this->_expired = $row['expired'];

        return $this->_token;
    }

    public function newest()
    {
        $sql = "SELECT id FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $res = $this->_conn->query($sql);
        if ($res !== FALSE) {
            $row = $res->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function save(array $over = []){
        $fields = [
            'token' => $this->_token,
            'created_at' => time(),
            'creation_ip' => $_SERVER['REMOTE_ADDR']
        ];

        $fields = array_merge($fields, $over);

        $columns = implode(',', array_keys($fields));
        $values = implode(',', array_values($fields));

        $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($values)";

        if (!$this->_conn->query($sql))
            die(DatabaseMisc::jsonError($this->_conn->error));

        return TRUE;
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
            return OAUTH_URL . $this->url = "/authorize?client_id=$client&response_type=code&username=$username&password=$password&action=Login";
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

// there can only be one refresh token at a time. It will always be the most recent token in the database.
class RefreshToken extends Token
{
    public function __construct($id = null, string $token = null)
    {
        parent::__construct($id, 'refresh-tokens', 9999);
        $this->_token = $token ?? null;
    }
}

class AccessToken extends Token
{
    private $_refresh;

    public function __construct($id = null)
    {
        parent::__construct($id, 'access_tokens', 600);
        $this->_refresh = $this->_exists ? $this->refreshToken() : NULL;
        $this->_conn = DatabaseMisc::connect();
    }

    private function refreshToken()
    {
        try {
            $sql = "SELECT refresh_token FROM " . $this->table . " WHERE id=" . $this->id;
            $res = $this->_conn->query($sql);
            $row = $res->fetch_assoc();
            return $row['refresh_token'];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function refresh()
    {
        if (!$this->_expired)
            return $this->_token;

        $config = parse_ini_file('config.ini');

        $client = $config['CLIENT_ID'];
        $username = $config['USERNAME'];
        $password = $config['PASSWORD'];

        $sql = "SELECT id, token, created_at FROM refresh_tokens ORDER BY id DESC LIMIT 1";
        $res = $this->_conn->query($sql);
        $row = $res->fetch_assoc();

        $refresh = new RefreshToken($row['id']);
        $this->_refresh = $refresh->get();



        $url = OAUTH_URL . "/token?grant_type=refresh_token&refresh_token=$refresh&client_id=$client&client_secret=$secret";

        return 'New Access Token, Refresh gets saved to DB';
    }

    private function getNew()
    {
        $auth = new AuthCode();
        $auth = $auth->get();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, OAUTH_URL . "/token?grant_type=authorization_code&code=$auth&client_id=$client&client_secret=$secret");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

        $refresh = new RefreshToken();

    }

    public function get()
    {
        if ($this->_exists){

            if (!$this->isValid()) {
                return $this->refresh();
            }

            parent::get();
            return $this->_token;
        }
        $this->getNew();
    }
}

$tets = new AccessToken();

$tets->get();
//print_r($tets->get() ? 'EGG' : 'FALSE');

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
        die(DatabaseMisc::jsonError($db->connect_error));
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
                if ($conn->query($sql) === FALSE) {
                    die(DatabaseMisc::jsonError($db->error));
                }
            }
        }
    } catch (\Throwable $th) {
        die($th);
    }

    $sql = "INSERT INTO access_tokens (code, created_at, creation_ip) VALUES ('$code', UNIX_TIMESTAMP()," . $_SERVER['REMOTE_ADDR'] . ")";

    if ($db->query($sql) === FALSE) {
        $json = ['error' => "Error: $sql " . $conn->error];
        die(json_encode($json));
    }

    return json_decode($response);
}

function getRestToken($access, $version = '*')
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://rest.bullhornstaffing.com/rest-services/login?version=$version&access_token=$access");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
    curl_setopt($ch, CURLOPT_POST, TRUE);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return json_decode($response);
}
