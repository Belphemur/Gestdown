<?php

require_once("conf.php");
$date=date("Y-m-d", time());
$db->pQuery('SELECT COUNT(date) FROM `stats` WHERE date=?', array('s', $date));
$nbre = $db->getVar();

if ($nbre == 0)
{
    $sqlTotalToday = 'INSERT INTO DownloadStats (Date,Serie,Type,Downloads,DirectDownloads,Episodes)
SELECT CURDATE() as DateNow, TotalStat.serie,  "Total", TotalStat.sum, DDL.sumDDL, TotalStat.nb
FROM (
    SELECT SUM(d.nbhits) AS sum,COUNT(d.id) AS nb, c.id as serie, c.nom as nom
    FROM categorie c
    LEFT JOIN downloads d
    ON d.categorie=c.id
    WHERE c.licencie!=1 AND c.stopped!=1
    GROUP BY c.nom
    ORDER BY c.nom ASC
) as TotalStat
LEFT JOIN
(
    SELECT SUM(ddl.downloads) as sumDDL, ddl.episode, c.id as catID
    FROM DirectDownloads ddl
    LEFT JOIN downloads d
    ON d.id = ddl.episode
    LEFT JOIN categorie c
    ON c.id = d.categorie
    GROUP BY c.id

) as DDL
ON DDL.catID = TotalStat.serie';

    $db->query($sqlTotalToday);
    $sqlDailyToday = 'INSERT INTO DownloadStats (Date,Serie,Type,Downloads,DirectDownloads,Episodes)
    (
        SELECT CURDATE() as DateNow,  Today.Serie, "Daily", (Today.Downloads - Yesterday.Downloads), (Today.DirectDownloads - Yesterday.DirectDownloads), (Today.Episodes - Yesterday.Episodes)
        FROM (
          SELECT * FROM DownloadStats WHERE Date = CURDATE() AND Type="Total"
        ) as Today
        JOIN
        (
           SELECT * FROM DownloadStats WHERE Date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
        ) as Yesterday
        ON Yesterday.serie = Today.serie AND
           Yesterday.Type = Today.Type
    )';
    $db->query($sqlDailyToday);

    echo "Stat générée avec succès";
}
else
    echo "Stat déja générée today";
?>