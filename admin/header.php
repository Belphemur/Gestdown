<?php 
require_once('conf.php');
$db->disableDiskCache();
include("login.php");
login();
$_SESSION['pseudo']=SessionManager::getInstance()->userName;
?>
<!DOCTYPE html>
<html>

    <head>
        <title>GestDown <?php echo $version; ?> :: Pour <?php echo $nom_site; ?></title>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="images/style.css" />
        <LINK href="images/live.css" rel="stylesheet" type="text/css">
        <script type="application/javascript" src="../js/jquery-1.6.2.min.js"></script>
        <script src="../js/verification_formulaire.js" type="application/javascript"></script>
        <script type="application/javascript" src="../js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="../js/jquery.datepick.pack.js"></script>
        <script type="application/javascript" src="../js/controller.js"></script>
        <script type="application/javascript" src="../js/formulaire_admin.js"></script>
        <script type="application/javascript" src="../js/jquery.form.js"></script>
        <script type="application/javascript" src="../js/modifDlAjax.js"></script>
        <link rel="stylesheet" href="../css/jquery.datepick.css" type="text/css" media="screen" charset="utf-8" />
    </head>

    <body>
        <div id="container">
            <div id="header"><br><br>
                        <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $version; ?></h2>
                        </div>

                        <div id="horizontal"><table width="547" border="0">
                                <tr>
                                    <th align="center"><a href="espace_admin_changeinfo.php">Votre Compte</a></th>
                                    <td align="center"><a href="gestion_dl.php">Séries</a></td>
                                    <td align="center"><a href="gestion_up.php">Upload/Mirror</a></td>
                                    <td align="center"><a href="gestion_admin.php">Admins</a></td>
                                    <td align="center"><a href="ges_stats.php">Statistiques</a></td>
                                    <td align="center"><a href="index.php?logout">Deconnexion</a></td>
                                </tr>
                            </table></div>

                        <div id="right-column">
