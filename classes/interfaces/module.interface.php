<?php
require_once("Erorr.class.php");
interface Module
{  
    function execute();  
    function __construct($id,$db);
	function __destruct();
 	function isValid();
	function execTime();
}  

?>