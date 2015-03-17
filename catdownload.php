<?PHP
$id=0;

if(isset($_GET['categorie']))
{
	$id=$_GET['categorie'];
	$redirect="http://www.gestdown.info/serie-$id.html";
}
else
	$redirect="http://www.gestdown.info";

header("Status: 301 Moved Permanently");
header("Location: $redirect");
exit();
?> 