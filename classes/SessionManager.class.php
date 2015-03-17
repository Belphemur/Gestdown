<?php
require_once 'Cipher.class.php';
/**
 * A simple class to access and set $_SESSION vars.
 *
 * @author Antoine Aflalo
 * @license http://www.gnu.org/licenses/gpl.html
 * @version 1.1
 * @example ./example.php simple example that's using this class.
 * @copyright     This file is part of SessionManager.
 *
 *   SessionManager is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   SessionManager is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *  along with SessionManager.  If not, see <http://www.gnu.org/licenses/>.
 */
class SessionManager
{
    private static $instance;
    protected $cipher,$crypted;
    /**
     * Constructor
     * @param string $name name of the session.
     */
    private function __construct($name="SessionManager", $crypted=true,$key='')
    {
        $this->start($name);
        $this->crypted=$crypted;
        if($this->crypted)
        {
            if($key=='')
                $this->cipher=new Cipher($_SERVER['SERVER_NAME']);
            else
                $this->cipher=new Cipher($key);
        }

    }
    public static function getInstance($name="SessionManager", $crypted=true,$key='')
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c($name,$crypted,$key);
        }

        return self::$instance;
    }
    /**
     * Simple Getter.
     * Check if session var exist and return it. Else return ''
     * @param string $name name of the session var.
     */
    public function __get($name)
    {
        if(!isset($_SESSION[$name]))
            throw new Exception("Session var ($name) not set.");
        if($this->crypted)
            return $this->cipher->decrypt($_SESSION[$name]);
        else
            return $_SESSION[$name];
    }
    /**
     * Simple Setter.
     * Set the session var
     * @param string $name name of the session var.
     * @param $value information to put in the session var.
     */
    public function __set($name,  $value)
    {
        if($this->crypted)
            $_SESSION[$name]=$this->cipher->encrypt($value);
        else
            $_SESSION[$name]=$value;
    }
    /**
     * Session ID regenerator.
     * regenerate the session ID.
     * @param bool $deleteOldSession delete the old session.
     */
    public function regenerateID($deleteOldSession=false)
    {
        return  session_regenerate_id($deleteOldSession);
    }
    /**
     * Session var checker.
     * return if the var is set.
     * @param string $name name of the session var.
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }
    /**
     * Session Destroyer.
     * delete the session vars and the session cookie (if isset).
     */
    public function destroy()
    {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies"))
        {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
            );
        }
        // Finally, destroy the session.
        session_destroy();
        $this->regenerateID(true);
    }
    /**
     * Session starter.
     * start the session if not already started.
     * @param string $name name of the session var.
     */
    public function start($name="SessionManager")
    {
        if(!isset($_SESSION))
        {
            session_name($name);
            session_start();
        }

    }
    public function __clone()
    {
        throw new Exception('Singleton, can\'t be cloned');
    }


}
?>
