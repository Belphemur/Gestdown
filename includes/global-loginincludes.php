<?php
// sEcure Login ---- library by abdohoo.com
if (! isset ( $_SESSION )) {
	session_start ();
}

include ("admin/functions/login-var.php");
include ("admin/functions/dojo-form.php");
include ("admin/functions/login-error.php");
include ("admin/functions/login_Brain.php");

$objechecklogin = new login_Brain;
$objdisplayerror = new display_error;

?>
