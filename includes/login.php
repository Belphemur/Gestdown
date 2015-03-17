<?php

function login()
{
    require_once('./admin/conf.php');
    require_once("./classes/Login.class.php");
    global $joomla,$sql_serveur,$sql_login, $sql_pass, $sql_bdd;
    $db = ezDB::getInstance();
    $db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
    
    try
    {
        $log = Login::getInstance(604800,'GestDown');
    }
    catch(Exception $e)
    {
        die($e->getMessage());
    }
    if(isset($_GET['logout']))
    {
        $log->logout();
        header("Location: index.php");
        die();
    }
    if($log->checkIfLogged())
        return true;
    else
    {
        if(isset($_POST['password'],$_POST['userName']))
        {
            try
            {
                
                $log->login($_POST['userName'],md5($_POST['password']));
                return true;
            }
            catch(Exception $e)
            {
                echo ($e->getMessage());
            }
        }
    }
    return false;
}

?>