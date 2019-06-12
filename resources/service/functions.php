<?php
define('OAUTH_URL', 'https://auth.bullhornstaffing.com/oauth');

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
    public static function jsonError(String $msg)
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

class Token
{
    private $_expiry = 600; // expiry time for UNIX epoch, default is 10 minutes
    public $table = 'access_tokens'; // the table that it looks for

    private $_conn;

    protected $id = 0;
    private $_token;
    private $_created_at = 0;
    private $_expired = FALSE;
    protected $_exists = FALSE;

    public function __construct(int $id = null, string $table, int $expiry, string $token = null)
    {
        $this->table = $table;
        $this->expiry = $expiry;
        $this->_conn = DatabaseMisc::connect();

        print_r("Token: parent construct<br>");
        if ($this->setToken($token)) {
            $this->_exists = FALSE;
            return;
        }

        print_r("Token: newest check<br>");
        $this->id = $id ?? $this->newest();

        if ($this->id == NULL) {
            print_r("Token: no ID<br>");
            return;
        }

        print_r('Token: ID is not null<br>');
        // $this->_exists = TRUE;
        $this->init();
        print_r('construct finished');
    }

    public function isValid()
    {
        print_r('isvalid check<br>');
        $this->_expired = (time() - $this->_created_at < $this->_expiry);

        print_r("isValid: ".$this->_created_at);

        die(time());

        if ($this->_expired) {
            $sql = "UPDATE " . $this->table . " expired=1 WHERE id=" . $this->id;
            if ($this->_conn->query($sql) === FALSE) {
                Log::fatal($this->_conn->error);
            }
            die('token expired');
            return FALSE;
        }
        log::info('token valid');
        return TRUE;
    }

    /**
     * Set the token, returns true if token is not null
     * @param string $token the token to test
     * @return bool
     */
    protected function setToken($token)
    {
        if ($token != NULL) {
            $this->_token = $token;
            return TRUE;
        }
        print_r("setToken: token is NULL<br>");

        return FALSE;
    }

    public function init()
    {
        print_r('Token: init()');
        $sql = "SELECT * FROM " . $this->table . " WHERE id=" . $this->id . " ORDER BY created_at DESC LIMIT 1";
        $res = $this->_conn->query($sql);

        if (!$res) {
            Log::fatal($this->_conn->error);
        }

        if ($res->num_rows <= 0)
            return FALSE;

        $row = $res->fetch_assoc();

        $this->_created_at = $row['created_at'];
        $this->_token = $row['token'];
        $this->_expired = $row['expired'];
        $this->_exists = true;
        
        print_r('<br>init returning<br>');

        return $this->_token;
    }

    /**
     * Gets the newest instance in the database and sets the ID
     * 
     * @return int|null newest token ID if match is found in the database, null if not
     */
    public function newest()
    {
        // die('getting newest');
        $sql = "SELECT * FROM " . $this->table . " ORDER BY id DESC LIMIT 1";
        $res = $this->_conn->query($sql);
        if ($res !== FALSE) {
            $row = $res->fetch_assoc();
            print_r("newest: ".implode(',',$row)."<br>");
            print_r("newest: ".$row['id']."<br>");
            return (int)$row['id'];
        }
        return null;
    }

    /**
     * Saves the Token to the database and returns the ID on successful save
     * 
     * @param array $over overflow params to get saved to the database, should be in key => value format
     * 
     * @return int the ID of the entity inserted in to the db. If it fails then die($msg) 
     */
    public function save(array $over = [])
    {
        $fields = [
            'token' => "'$this->_token'",
            'created_at' => time(),
            'creation_ip' => "'" . $_SERVER['REMOTE_ADDR'] . "'"
        ];

        $fields = array_merge($fields, $over);

        $columns = implode(",", array_keys($fields));
        $values = implode(",", array_values($fields));

        $sql = "INSERT INTO $this->table ( $columns ) VALUES ( $values )";

        if (!$this->_conn->query($sql))
            Log::fatal($this->_conn->error);

        $this->id = $this->_conn->insert_id;
        return $this->id;
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
            return OAUTH_URL . "/authorize?client_id=$client&response_type=code&username=$username&password=$password&action=Login";
        } catch (Exception $e) {
            echo $e;
            return null;
        }
    }

    public static function get()
    {
        $url = AuthCode::buildUrl(parse_ini_file('config.ini'));
        // die($url);
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, AuthCode::$params);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                Log::fatal(curl_error($ch));
                die('rats... '.curl_error($ch));
            }

            curl_close($ch);

            if (preg_match("|Location: (https?://\S+)|", $response, $m)) {
                //Location is in $m[1]
                if (preg_match("|code=(\S+)\&client_id|", $m[1], $n)) {
                    $code = urldecode($n[1]);
                    return $code;
                }
            }
            return FALSE;
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
        parent::__construct($id, 'refresh_tokens', 9999, $token);
    }
}

class AccessToken extends Token
{
    private $_refresh;

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

    public function __construct(int $id = null, string $token = null)
    {
        parent::__construct($id, 'access_tokens', 600, $token);
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
            Log::fail($e->getMessage());
        }
    }

    public function refresh()
    {
        die('refreshing token');
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

    public static function get(){
        $auth = AuthCode::get();
        $config = parse_ini_file('config.ini');
        $client = $config['CLIENT_ID'];
        $secret = $config['CLIENT_SECRET'];

        $ch = curl_init(OAUTH_URL . "/token?grant_type=authorization_code&code=$auth&client_id=$client&client_secret=$secret");
        curl_setopt_array($ch, AccessToken::$params);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function getNew()
    {
        print_r('accestoken: getNew');
        $auth = new AuthCode();
        $auth = $auth->get();

        $config = parse_ini_file('config.ini');
        $client = $config['CLIENT_ID'];
        $secret = $config['CLIENT_SECRET'];

        $ch = curl_init(OAUTH_URL . "/token?grant_type=authorization_code&code=$auth&client_id=$client&client_secret=$secret");
        curl_setopt_array($ch, $this->newParams);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        $refresh = new RefreshToken(null, $response->refresh_token);
        $this->_refresh = $refresh->save();
        $this->setToken($response->access_token);
        $this->save(['refresh_token' => $this->_refresh]);

        return $this->_token;
    }

    public function getOld()
    {
        print_r('child get<br>');
        if ($this->_exists) {
            print_r('token exists');
            if (!$this->isValid()) {
                Log::info('token invalid, refreshing');
                return $this->refresh();
            }

            return $this->_token;
        }
        $this->getNew();
    }
}

// $tets = new AccessToken();
// $tets->getNew();
// print_r($tets->getNew());
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
    } catch (Exception $e) {
        die($e->getMessage());
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


print_r(AuthCode::get());