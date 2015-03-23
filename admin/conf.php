<?php
error_reporting(E_ALL | E_NOTICE);

//////////////////////////////////
///     Modifier ci-dessous    ///
//////////////////////////////////
//Votre site\\
$nom_site = 'Ame no Tsuki'; //Nom de votre site
$email_admin = 'balor@gestdown.info'; //Votre email
$url_site = 'https://www.gestdown.info/'; //Adresse de votre site
$page_dl ='index'; //nom de la premi�re page
$joomla=false;

//Conf pour le FTP\\
/*$ftp_server="ftp.free-h.org";
$ftp_user_name="amenots_ftp15";
$ftp_user_pass="a813608955b2";*/

$ftp_server="localhost";
$ftp_user_name="balorame";
$ftp_user_pass="Tsuki1889";

$source_file="../sorties.xml";
$destination_file="/web/sorties.xml";

//Pr�frences\\
$valid_dl = '2';//Permet de valider automatiquement les Downloads propos� par vos visiteurs, 1 pour OUI, et 2 pour NON
$permettre_dl ='0'; //Permettre au visiteur de pouvoir ajouter un Download 0 pour OUI 1 pour NON
$nbre_page = '5';//Ceci est le nombre de fichier (Downloads) � afficher par page

//Version du script\\
$version = 'V2.8';//Ne pas toucher, ceci est votre version actuelle du script!
//Votre Base de donn�e mySQL\\
$sql_serveur = '127.0.0.1'; //Serveur de la Base De Donn�e (BDD) (Par exemple : localhost)
$sql_login = 'c1mysql'; //le Login de votre base de donn�e, habituellement votre pseudo
$sql_pass = 'b3myhtx9'; //Le passe d'acc�s � votre base de donn�es
$sql_bdd = 'c1mysql'; //Le nom de votre base de donn�e
$timeout=60;
//$diskCache='/var/www/gestdown.info/tmp/';
$diskCache='D:/Temp/cache';
$tmpDir = $diskCache;

//Episodes Config
$episodeDir ="D:\\Temp\\";
//$episodeDir = '/home/ant/Episodes/';
$episodeHttpPath ='/Gestdown/series/';

//Accepted Extension
$epExt = '{mkv,avi,mp4}';
$epExtArray = array('mp4','mkv','avi');


///JHEBERG
$jheberg['user'] = 'anttsuki';
$jheberg['pass'] = 'Tsuki1889';
$jheberg['url']='http://jheberg.net/api/get/server/';
$jheberg['upload_page']='api/upload/';
#$jheberg['url'] = 'http://zoidberg.jheberg.net/jheberg/index.php?method=apiUpload';

//Imgur
$imgur['CLIENT_ID'] = 'a3e3f8a2143e663';
$imgur['CLIENT_SECRET'] = 'f20002265f4d5132f5450925772151fc5a8ac5f5';

//////////////////////////////////
/// Ne pas Modifier ci-dessous ///
//////////////////////////////////
/*if(strpos(dirname($_SERVER['PHP_SELF']),'admin'))
	require_once("../includes/autoload.php");
else if(strpos(dirname($_SERVER['PHP_SELF']),'includes'))
	require_once("autoload.php");
else
	require_once("includes/autoload.php");*/
	
/**
 *
 * @param string $classname Class or Interface name automatically
 *              passed to this function by the PHP Interpreter
 */
function __autoload($classname){
    //Directories added here must be
//relative to the script going to use this file.
//New entries can be added to this list
    $directories = array(
      '',
      'classes/',
	  '../classes/',
    );

    //Add your file naming formats here
    $fileNameFormats = array(
      '%s.php',
      '%s.class.php',
      'class.%s.php',
      '%s.inc.php'
    );

    // this is to take care of the PEAR style of naming classes
    $path = str_ireplace('_', '/', $classname);
    if(@include_once $path.'.php'){
        return;
    }
   
    foreach($directories as $directory){
        foreach($fileNameFormats as $fileNameFormat){
            $path = $directory.sprintf($fileNameFormat, $classname);
            if(file_exists($path)){
                require_once $path;
                return;
            }
        }
    }
}

DEFINE('TEMPLATE', './includes/template.php');


$db= ezDB::getInstance();
// Specify a cache dir. Path is taken from calling script
//$db->enableDiskCache($diskCache,$timeout);

// (1. You must create this dir. first!)
// (2. Might need to do chmod 775)

// Global override setting to turn disc caching off
// (but not on)

// By wrapping up queries you can ensure that the default
// is NOT to cache unless specified
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);

//$cipher=new Cipher($sql_bdd.$nom_site.$sql_login);
$db->query("SET NAMES 'utf8'");

function genere_passwd() {
	$tpass=array();
	$id=0;
	$taille=6;
	// r�cup�ration des chiffres et lettre
	for($i=48;$i<58;$i++) $tpass[$id++]=chr($i);
	for($i=65;$i<91;$i++) $tpass[$id++]=chr($i);
	for($i=97;$i<123;$i++) $tpass[$id++]=chr($i);
	$passwd="";
	for($i=0;$i<$taille;$i++) {
		$passwd.=$tpass[rand(0,$id-1)];
	}
	return $passwd;
} 

// Simulation de � register_globals = On �.
// Astuce fournie par Toocharger.com.

if (!ini_get('register_globals')){
    if (phpversion() < "4.1.0"){
        $_GET     =& $HTTP_GET_VARS;
        $_POST    =& $HTTP_POST_VARS;
        $_COOKIE  =& $HTTP_COOKIE_VARS;
        $_SERVER  =& $HTTP_SERVER_VARS;
        $_ENV     =& $HTTP_ENV_VARS;
        $_SESSION =& $HTTP_SESSION_VARS;
    }

    extract($_GET, EXTR_OVERWRITE);
    extract($_POST, EXTR_OVERWRITE);
    extract($_COOKIE, EXTR_OVERWRITE);
    extract($_SERVER, EXTR_OVERWRITE);
    extract($_ENV, EXTR_OVERWRITE);

    if (isset($_SESSION) && is_array($_SESSION))
        extract($_SESSION, EXTR_OVERWRITE);
}

$close = '&copy;2009 - Propuls&eacute; par GestDown ' .$version. ' Cr�er par Dark_Balor';//Close
?>
