<?php

require_once 'ezDB.class.php';
require_once 'SessionManager.class.php';
require_once 'Cipher.class.php';

/**
 * Description of Loginclass
 * Login singletons is a simple class that can be used to log a user
 * @author Antoine Aflalo
 */
class Login {

    protected $db, $userName, $userID, $ttl, $userIP, $CoSname, $dbColumns, $sess, $cipher, $byCheckCookie;
    public static $instance;

    private function __construct($ttl, $name) {
        $this->db = ezDB::getInstance();
        $this->CoSname = $name;
        $this->ttl = $ttl;
        $this->cipher = new Cipher($_SERVER['SERVER_NAME'] . $this->CoSname);
        $this->dbColumns = array('table' => 'admin_information', 'id' => 'admin_id', 'pass' => 'admin_password', 'user' => 'admin_username');
        $this->sess = SessionManager::getInstance($this->CoSname . 'Session', false);
        $this->CoSname.='Cookie';
        $this->byCheckCookie = false;
        if (isset($this->sess->userName)) {
            $this->userName = $this->sess->userName;
            $this->userID = $this->sess->userID;
            $this->userIP = $this->sess->ip;
        }
        else
            $this->checkCookie();
    }

    /**
     * function to get an instance of Login (that is a singleton)
     * @param <integer> $ttl just the ttl of the possible cookie
     * @param string $name the name of the session and the cookie
     */
    public static function getInstance($ttl=604800, $name='Villo') {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c($ttl, $name);
        }

        return self::$instance;
    }

    /**
     * function to check if there is a login cookie and parse the value of it
     */
    protected function checkCookie() {
        if (!isset($this->sess->userName) && isset($_COOKIE[$this->CoSname])) {
            $tabCookie = $this->cipher->decrypt($_COOKIE[$this->CoSname]);
            if (preg_match('/(\d)*:"([^":]*)"/', $tabCookie)) {
                $tabCookie = unserialize($tabCookie);
                $userAgent = $tabCookie['userAgent'];
                if ($userAgent == sha1($_SERVER['HTTP_USER_AGENT'] . $this->CoSname)) {
                    $this->byCheckCookie = true;
                    $this->login($tabCookie['userName'], substr($tabCookie['pass'], 0, -40), false);
                    $this->byCheckCookie = false;
                } else {
                    setcookie($this->CoSname, '', time() - 3600, '/');
                    throw new Exception("Hack de cookie détecté, veuillez vous reconnecter.");
                }
            } else {
                setcookie($this->CoSname, '', time() - 3600, '/');
                throw new Exception("Problème de cookie, veuillez réessayer.");
            }
        }
    }

    /**
     * function to check if the user is logged.
     */
    function checkIfLogged() {
        $this->sess->regenerateID();
        if (isset($this->sess->userName, $this->sess->userID, $this->sess->HTTP_USER_AGENT) && $this->sess->HTTP_USER_AGENT == (sha1($_SERVER['HTTP_USER_AGENT'] . $this->CoSname)) && $this->userIP == $this->getIp())
            return true;
        else
            return false;
    }

    /**
     * function to set the database information for the login
     * @param string $table with table to use
     * @param string $userColumn what's the label of the user column
     * @param string $passColumn label of the pass column
     * @param string $idColumn label of the id column
     */
    function setDBinfo($table, $userColumn, $passColumn, $idColumn) {
        $this->dbColumns = array('table' => $table, 'id' => $idColumn, 'pass' => $passColumn, 'user' => $userColumn);
    }

    /**
     * function to log the user
     * @param string $user username
     * @param string $encryptedPass encrypted password to check db
     * @param bool $setCookie if we need to set a cookie
     */
    function login($user, $encryptedPass, $setCookie=true) {
        $query = "SELECT COUNT(*) count, {$this->dbColumns['id']} userID
    FROM
    `{$this->dbColumns['table']}`
    WHERE {$this->dbColumns['user']}=? AND {$this->dbColumns['pass']}=?
    GROUP BY {$this->dbColumns['id']}
    LIMIT 1";
        try {
            $this->db->pQuery($query, array('ss', $user, $encryptedPass));
        } catch (Exception $e) {
            throw new Exception('Login ERROR : ' . $e->getMessage() . '(' . $e->getCode() . ')');
        }
        $result = $this->db->getRow();
        if (!empty($result)) {
                $this->userName=$user;
                $this->userID=$result->userID;
                $this->userIP=$this->getIp();
                $this->setSessionVars();
                if($setCookie)
                    $this->setCookie($encryptedPass);
            }
        else {
            if ($this->byCheckCookie) {
                setcookie($this->CoSname, '', time() - 3600, '/');
                throw new Exception("Problème de cookie, veuillez réessayer.");
            }
            else
                throw new Exception("Utilisateur non trouvé", 1);
        }
    }

    /**
     * function to logout the current user by destroying the session AND the cookie
     */
    function logout() {
        $this->sess->destroy();
        // Set expiration time to -1hr (will cause browser deletion)
        setcookie($this->CoSname, '', time() - 3600, '/');
        // Unset key
    }

    protected function setSessionVars() {
        $this->sess->userName = $this->userName; // only database have information
        $this->sess->userID = $this->userID; // only database have information
        $this->sess->ip = $this->userIP;
        $this->sess->HTTP_USER_AGENT = sha1($_SERVER['HTTP_USER_AGENT'] . $this->CoSname);
    }

    protected function setCookie($pass) {
        $cookieExp = time() + $this->ttl;
        $cookie = array('userName' => $this->sess->userName, 'userID' => $this->sess->userID, 'pass' => $pass . sha1($this->CoSname), 'userAgent' => $this->sess->HTTP_USER_AGENT);
        $cookie = $this->cipher->encrypt(serialize($cookie));

        setcookie($this->CoSname, $cookie, $cookieExp, '/');
    }

    /**
     * Prevent the cloning of the singleton
     */
    public function __clone() {
        trigger_error('Cloning not allowed', E_USER_ERROR);
    }

    /**
     * Function to discover the ip of the user.
     */
    protected function getIp() {
        global $REMOTE_ADDR;
        global $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR, $HTTP_FORWARDED;
        global $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM;
        // Get some server/environment variables values
        if (empty($REMOTE_ADDR)) {
            if (!empty($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
                $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
            } else if (!empty($_ENV) && isset($_ENV['REMOTE_ADDR'])) {
                $REMOTE_ADDR = $_ENV['REMOTE_ADDR'];
            } else if (@getenv('REMOTE_ADDR')) {
                $REMOTE_ADDR = getenv('REMOTE_ADDR');
            }
        } // end if
        if (empty($HTTP_X_FORWARDED_FOR)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED_FOR'])) {
                $HTTP_X_FORWARDED_FOR = $_ENV['HTTP_X_FORWARDED_FOR'];
            } else if (@getenv('HTTP_X_FORWARDED_FOR')) {
                $HTTP_X_FORWARDED_FOR = getenv('HTTP_X_FORWARDED_FOR');
            }
        } // end if
        if (empty($HTTP_X_FORWARDED)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_X_FORWARDED'])) {
                $HTTP_X_FORWARDED = $_SERVER['HTTP_X_FORWARDED'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_X_FORWARDED'])) {
                $HTTP_X_FORWARDED = $_ENV['HTTP_X_FORWARDED'];
            } else if (@getenv('HTTP_X_FORWARDED')) {
                $HTTP_X_FORWARDED = getenv('HTTP_X_FORWARDED');
            }
        } // end if
        if (empty($HTTP_FORWARDED_FOR)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                $HTTP_FORWARDED_FOR = $_SERVER['HTTP_FORWARDED_FOR'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED_FOR'])) {
                $HTTP_FORWARDED_FOR = $_ENV['HTTP_FORWARDED_FOR'];
            } else if (@getenv('HTTP_FORWARDED_FOR')) {
                $HTTP_FORWARDED_FOR = getenv('HTTP_FORWARDED_FOR');
            }
        } // end if
        if (empty($HTTP_FORWARDED)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_FORWARDED'])) {
                $HTTP_FORWARDED = $_SERVER['HTTP_FORWARDED'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_FORWARDED'])) {
                $HTTP_FORWARDED = $_ENV['HTTP_FORWARDED'];
            } else if (@getenv('HTTP_FORWARDED')) {
                $HTTP_FORWARDED = getenv('HTTP_FORWARDED');
            }
        } // end if
        if (empty($HTTP_VIA)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_VIA'])) {
                $HTTP_VIA = $_SERVER['HTTP_VIA'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_VIA'])) {
                $HTTP_VIA = $_ENV['HTTP_VIA'];
            } else if (@getenv('HTTP_VIA')) {
                $HTTP_VIA = getenv('HTTP_VIA');
            }
        } // end if
        if (empty($HTTP_X_COMING_FROM)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_X_COMING_FROM'])) {
                $HTTP_X_COMING_FROM = $_SERVER['HTTP_X_COMING_FROM'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_X_COMING_FROM'])) {
                $HTTP_X_COMING_FROM = $_ENV['HTTP_X_COMING_FROM'];
            } else if (@getenv('HTTP_X_COMING_FROM')) {
                $HTTP_X_COMING_FROM = getenv('HTTP_X_COMING_FROM');
            }
        } // end if
        if (empty($HTTP_COMING_FROM)) {
            if (!empty($_SERVER) && isset($_SERVER['HTTP_COMING_FROM'])) {
                $HTTP_COMING_FROM = $_SERVER['HTTP_COMING_FROM'];
            } else if (!empty($_ENV) && isset($_ENV['HTTP_COMING_FROM'])) {
                $HTTP_COMING_FROM = $_ENV['HTTP_COMING_FROM'];
            } else if (@getenv('HTTP_COMING_FROM')) {
                $HTTP_COMING_FROM = getenv('HTTP_COMING_FROM');
            }
        } // end if
        // Gets the default ip sent by the user
        if (!empty($REMOTE_ADDR)) {
            $direct_ip = $REMOTE_ADDR;
        }

        // Gets the proxy ip sent by the user
        $proxy_ip = '';
        if (!empty($HTTP_X_FORWARDED_FOR)) {
            $proxy_ip = $HTTP_X_FORWARDED_FOR;
        } else if (!empty($HTTP_X_FORWARDED)) {
            $proxy_ip = $HTTP_X_FORWARDED;
        } else if (!empty($HTTP_FORWARDED_FOR)) {
            $proxy_ip = $HTTP_FORWARDED_FOR;
        } else if (!empty($HTTP_FORWARDED)) {
            $proxy_ip = $HTTP_FORWARDED;
        } else if (!empty($HTTP_VIA)) {
            $proxy_ip = $HTTP_VIA;
        } else if (!empty($HTTP_X_COMING_FROM)) {
            $proxy_ip = $HTTP_X_COMING_FROM;
        } else if (!empty($HTTP_COMING_FROM)) {
            $proxy_ip = $HTTP_COMING_FROM;
        } // end if... else if...
        // Returns the true IP if it has been found, else FALSE
        if (empty($proxy_ip)) {
            // True IP without proxy
            return $direct_ip;
        } else {
            $is_ip = preg_match('|^([0-9]{1,3}\.){3,3}[0-9]{1,3}|', $proxy_ip, $regs);
            if ($is_ip && (count($regs) > 0)) {
                // True IP behind a proxy
                return $regs[0];
            } else {
                // Can't define IP: there is a proxy but we don't have
                // information about the true IP
                return FALSE;
            }
        } // end if... else...
    }

}

?>
