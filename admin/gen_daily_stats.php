<?php

require_once("conf.php");
$date=date("Y-m-d", time());
$db->pQuery('SELECT COUNT(date) FROM `stats` WHERE date=?', array('s', $date));
$nbre = $db->getVar();

if ($nbre == 0)
{
    $sqlTotalToday = 'INSERT INTO DownloadStats (Date,Serie,Type,Downloads,DirectDownloads,Episodes)
SELECT CURDATE() as DateNow, TotalStat.serie,  "Total", TotalStat.sum, TotalStat.DDL, TotalStat.nb FROM (
    SELECT SUM(d.nbhits) AS sum,COUNT(d.id) AS nb, c.id as serie, c.nom as nom,
    (
      SELECT SUM(ddl.downloads) FROM DirectDownloads ddl WHERE ddl.episode=d.id GROUP BY d.id
    ) as DDL
    FROM categorie c
    JOIN downloads d
    ON d.categorie=c.id
    WHERE c.licencie!=1 AND c.stopped!=1
    GROUP BY c.nom
    ORDER BY c.nom ASC
) as TotalStat';

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