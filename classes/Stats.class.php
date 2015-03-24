<?php

require_once("CAsBarDiagram.php");

class Stats
{

    private $_date, $db, $dailyStats, $totalStats, $bug = false;

    function __construct($date, ezDB $db)
    {
        $this->db = $db;
        $this->dailyStats = array();
        $this->totalStats = array();
        try {
            try {
                $this->setDate($date);
            } catch (Exception $e) {

                $this->setDate(strtotime($this->_date) - (24 * 3600));
            }
        } catch (Exception $e) {

            $this->bug = true;
        }
    }

    function __destruct()
    {
        unset($this->db);
    }

    private function exist()
    {
        $sql = 'SELECT ds.DirectDownloads, ds.Downloads,ds.Episodes, c.nom AS nom
                FROM DownloadStats ds
                JOIN categorie c
                ON c.id = ds.Serie
                WHERE ds.Type=? AND ds.Date=?
                ORDER BY nom ASC';
        if (!$this->db->pQuery($sql, array('ss', 'Total', $this->_date))) {
            throw new Exception('Aucune statistique trouvée à cette date', 1);
        } else {
            $total = $this->db->getResults();
            $this->db->pQuery($sql, array('ss', 'Daily', $this->_date));
            $daily = $this->db->getResults();
            foreach ($total as $id => $value) {
                $this->totalStats[$value->nom] = $value;
                $this->dailyStats[$value->nom] = $daily[$id];
            }
        }
    }

    function setDate($new_date)
    {
        if (is_numeric($new_date))
            $this->_date = date("Y-m-d", $new_date);
        else
            $this->_date = date("Y-m-d", strtotime($new_date));
        $this->exist();
    }

    private function generateGraph($img, $graph_title, $stats)
    {
        $s_dls = array();
        $s_names = array();
        $s_eps = array();
        $s_ddl = array();
        $output = '';
        foreach ($stats as $name => $stat) {
            $s_names[] = $stat->nom;
            $s_dls[] = $stat->Downloads;
            $s_ddl[] = $stat->DirectDownloads;
            $s_eps[] = $stat->Episodes;
        }
        $total_day = array_sum($s_dls);
        $total_eps = array_sum($s_eps);
        $total_ddl = array_sum($s_ddl);
        $legend_x = array('Téléchargements', 'Dont DDL',  'Episodes');
        $legend_y = $s_names;
        $graph = new CAsBarDiagram;
        $graph->bwidth = 8; // set one bar width, pixels
        $graph->bt_total = 'Total'; // 'totals' column title, if other than 'Totals'
        $graph->showtotals = 0;  // uncomment it if You don't need 'totals' column
        $graph->precision = 0;  // decimal precision
        // call drawing function
        $graph->imgpath = $img;
        if ($graph_title == NULL)
            $graph_title = "Statistiques au " . date("d-m-y", strtotime($this->_date));
        ob_start();
        $graph->DiagramBar($legend_x, $legend_y, array($s_dls, $s_ddl ,$s_eps), $graph_title);
        echo "<tr class='barhead'><td nowrap><span style=\"color:red;\">Total général</span></td>

		   <td align=right nowrap>&nbsp; $total_day &nbsp;</td>
		     <td align=right nowrap><b>&nbsp; $total_ddl  &nbsp;</b></td>
		   <td align=right nowrap><b>&nbsp; $total_eps  &nbsp;</b>
		   <td align=right nowrap><b>&nbsp;  &nbsp;</b></tr>
		</table><!-- 001 finish -->\n</p>";
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function compare($end_d, $daily = false, $path = "//www.gestdown.info/images/graph/")
    {
        $dailyS = $this->dailyStats;
        $totalS = $this->totalStats;
        $startDate = $this->_date;
        $this->setDate($end_d);
        if($daily) {
            $endStat = &$this->dailyStats;
            $startStat = &$dailyS;
            $statTitle =  'Statistiques Journalières entre ';
        } else {
            $endStat = &$this->totalStats;
            $startStat = &$totalS;
            $statTitle =  'Statistiques Générale entre ';
        }
            foreach($endStat as $key=>$stat) {
                if(!isset($startStat[$key]))
                    continue;
                $endStat[$key]->Downloads = $stat->Downloads - $startStat[$key]->Downloads;
                $endStat[$key]->DirectDownloads = $stat->DirectDownloads - $startStat[$key]->DirectDownloads;
                $endStat[$key]->Episodes = $stat->Episodes - $startStat[$key]->Episodes;

        }
        return $this->generateGraph($path, $statTitle. date("d-m-y", strtotime($startDate)) . ' et ' .  date("d-m-y", strtotime($this->_date)), $endStat);
    }

    function daily_display($img = "//www.gestdown.info/images/graph/")
    {
        return $this->generateGraph($img, "Statistiques Journalières au " . date("d-m-y", strtotime($this->_date)), $this->dailyStats);
    }

    function total_display($img = "//www.gestdown.info/images/graph/")
    {
        return $this->generateGraph($img, "Statistiques Totales au " . date("d-m-y", strtotime($this->_date)), $this->totalStats);
    }

}

?>