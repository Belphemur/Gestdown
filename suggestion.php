<?php
require('./admin/conf.php');
SessionManager::getInstance('GestDownSession',false);
header('Content-Type: text/html; charset=utf8');
$testcaptcha = new MathCaptcha();
$msg_erreur = "Erreur. Les champs suivants doivent Ãªtre obligatoirement remplis :<br/><br/>";
$msg_ok = "RÃ©sumÃ© envoyÃ©, un Administrateurs vÃ©rifiera rapidement votre suggestion.<br />";
$message = $msg_erreur;
if(!isset($_POST['mort']))
{
    if (empty($_POST['resume']))
        $message .= "Resumé".PHP_EOL;
    else if(strlen($_POST['resume']) > 1100)
        $message .="1100 charactères max".PHP_EOL;
    if (empty($_POST['auteur']))
        $message .= "Auteur".PHP_EOL;
    if(!empty($_POST['screen']))
        if(!filter_var($_POST['screen'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
            $message .= "Lien du screen invalide".PHP_EOL;
    if(isset($_POST['math_captcha']) && is_numeric($_POST['math_captcha']))
    {
        $answer = intval($_POST['math_captcha']);
        if(!$testcaptcha->checkAnswer($answer,'sug_'.$id))
            $message="Réponse au calcul erronée".PHP_EOL;
    }
    else
        $message.="Calcul mathématique".PHP_EOL;

    $requete="SELECT id FROM `descriptions` WHERE `auteur`='$auteur' AND `download`='$id'";
    if($db->query($requete))
        $message="Vous avez déjà  proposé un résumé pour cet épisode, veuillez patienter le temps qu'un administrateur le lise";

    if ($message!=$msg_erreur)
    {
        die($message);
    }
    else
    {
        foreach($_POST as $index => $valeur)
        {
            $$index = trim($valeur);
        }
        $sql = "INSERT INTO descriptions VALUES ('', '$id', '$resume', '$auteur','$screen',0)";
        $res = $db->query($sql);
        echo $msg_ok;


    }
    unset($db);
    if(isset($_SESSION["solution_sug_$id"]))
    {
        $_SESSION["solution_sug_$id"]=NULL;
        unset($_SESSION["solution_sug_$id"]);
    }
}
else
{
    if (empty($_POST['explication']))
        $message .= "Explications";
    if(!empty($_POST['math_captcha']))
    {
        $answer = $_POST['math_captcha'];
        if(!$testcaptcha->checkAnswer($answer,$id))
            $message="Réponse au calcul erronée".PHP_EOL;
    }
    else
        $message.="Calcul mathématique".PHP_EOL;

    if ($message!=$msg_erreur)
    {
        die($message);
    }
    foreach($_POST as $index => $valeur)
    {
        $$index = trim($valeur);
    }
    $qualite=$_POST['quality'];
    $date= date("( j/n/y )",time());
    $explication="<u>Lien $qualite </u>$date :<br />\n".$explication;
    $requete = "UPDATE downloads SET mort=1 WHERE id=$id";
    $db->query($requete);
    $db->query("INSERT INTO descriptions VALUES ('', '$id', '$explication', 'LIEN_MORT','',1)");
    echo 'Merci de nous avoir signalé ce lien mort';

    if(isset($_SESSION['solution_'.$id]))
    {
        $_SESSION['solution_'.$id]=NULL;
        unset($_SESSION['solution_'.$id]);
    }

    unset($db);
}

?>