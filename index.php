<?php
require_once("admin/conf.php");

$vue = new View("./templates/vues/");
$titre = "Ame no Tsuki [AnT] (Gestdown) : ";
$meta_desc = "Site centralisant les épisode de la Ame No Tsuki qui est une team de fansub. Le site contient tous les liens de la team, les épisodes, un forum et plein d'autre informations. Notre boulot consiste à vous faire découvrir la japanimation, nous traduisons et sous-titrons plusieurs séries d'animations japonaises.";

if (isset($_GET['id']))
    $id = $_GET['id'];
$header_js = "";
if (isset($_GET['ext'])) {
    $vue->serie = new Serie($id,$db);
    $titre .= $vue->serie->getNom();
    $viewFile="v_serie.php";
    if (isset($_GET['ep'])) {

    } else if (isset($_GET['serie'])) {

    }
} else {
    $titre = "Gestdown : Centralisation des épisodes de la Ame no Tsuki [AnT]";
    $series = $db->getResults("SELECT id,nom,image, finie, licencie,stopped, width, height FROM categorie WHERE nom!='Prob de lien' ORDER BY nom ASC");
    $vue->series = $series;
    $viewFile="v_index3.php";
}
$vue->header_js = $header_js;
$vue->titre = $titre;
$vue->meta_desc = $meta_desc;
echo $vue->render($viewFile);

