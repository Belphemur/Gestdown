<?php

/**
 * Objet permetant de contenir les résultats d'une fonction
 * @package RESULT
 * @access public
 * @author Antoine Aflalo (antoineaf@gmail.com)
 * @return none
 * @version 1.2.0;
 */
class Result {

    private $_vars, $_id;
    private static $id = 0;

    /**
     * Constructeur de la classe
     *
     * @param none
     */
    public function __construct() {
        $this->_id = ++self::$id;
        $this->_vars = array();
    }

    /**
     * Retourne le vecteur _vars
     *
     * @param none
     * @return array _vars
     */
    public function get_vars() {
        return $this->_vars;
    }

    /**
     * Set le vecteur _vars
     *
     * @param array $vars
     * @return none
     */
    protected function set_vars($vars) {
        $this->_vars = $vars;
    }

    /**
     * Admet une variable cree au vol
     *
     * @param string $key
     * @param mix $val
     */
    public function __set($key, $val) {
        $this->_vars[$key] = $val;
    }

    public function set($key, $val) {
        $this->__set($key, $val);
    }

    /**
     * Retourne la valeur cree par le __set() magique
     * Retourne faux si la valeur demandée n'existe pas
     *
     * @param string $key
     * @return mix
     */
    public function __get($key) {
        if (isset($this->_vars[$key]))
            return $this->_vars[$key];
        else
            return false;
    }

    /**
     * Affiche ce que contient le vecteur var avec le nom de la clef
     *
     * @param none
     * @return none
     */
    public function display() {
        echo $this;
    }

    function __toString() {
        $str = '';
        foreach ($this->_vars as $key => $value)
            $str.='[' . $this->_id . ']' . $key . " => " . $value . "<br /> \n";
        return $str;
    }

    /**
     * Permet de fusionner deux result
     *
     * @param Result $from
     * @return none
     */
    public function add(Result $from, $replace=true) {
        foreach ($from->_vars as $key => $vars)
        {
            if (isset($this->_vars[$key]) && $replace)
                $this->_vars[$key] = $vars;
            if (isset($this->_vars[$key]) && !$replace && is_array($this->_vars[$key]))
                if (is_array($from->_vars[$key]))
                    $this->_vars[$key] = array_merge_recursive($this->_vars[$key], $from->_vars[$key]);
                else
                    $this->_vars[$key] = array_push($this->_vars[$key], $from->_vars[$key]);
            else if (!isset($this->_vars[$key]))
                $this->_vars[$key] = $vars;
        }
    }

}

?>
