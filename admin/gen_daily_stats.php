<?php

require_once("conf.php");
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$date=date("Y-m-d", time());
$db->pQuery('SELECT COUNT(date) FROM `stats` WHERE date=?', array('s', $date));
$nbre = $db->getVar();

if ($nbre == 0)
{
    $totalStats = $db->getResults("SELECT SUM(d.nbhits) AS sum,COUNT(d.id) AS nb, c.nom FROM categorie c
JOIN downloads d
ON d.categorie=c.id
WHERE c.licencie!=1 AND c.stopped!=1
GROUP BY c.nom
ORDER BY c.nom ASC");
    $dls_today = array();
    foreach ($totalStats as $data)
    {

        $dls_today[$data->nom] = $data->sum;
        $episodes[$data->nom] = $data->nb;
    }
    $serial_today = serialize($dls_today);
    $serial_ep = serialize($episodes);
    $db->query("INSERT INTO `stats` (`date`, `dls`, `ep`) VALUES ('$date', '$serial_today','$serial_ep')");

    $date_yesterday = date("Y-m-d", time() - (24 * 3600));
    if ($db->query("SELECT dls FROM stats WHERE date='$date_yesterday'"))
    {
        $dls_yesterday = array();
        $dls_yesterday = unserialize($db->getVar());
        $daily = array();
        foreach ($dls_today as $nom => $stat)
        {
            if (isset($dls_yesterday[$nom]))
                $daily[$nom] = $stat - $dls_yesterday[$nom];

            else
                $daily[$nom] = $stat;
        }
        $db_daily = serialize($daily);
        $db->query("UPDATE `stats` SET `daily` = '$db_daily' WHERE `stats`.`date` = '$date' LIMIT 1 ");
    }
    echo "Stat générée avec succès";
}
else
    echo "Stat déja générée today";
?>