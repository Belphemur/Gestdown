<?php

require_once("CAsBarDiagram.php");

class Stats {

    private $date_, $db, $Dstats, $Tstats, $results, $eps, $bug = false;

    function __construct($date, $db) {
        if (is_numeric($date))
            $this->date_ = date("Y-m-d", $date);
        else
            $this->date_ = date("Y-m-d", strtotime($date));
        $this->db = $db;
        $this->stats = array();
        try
        {
            try
            {
                $this->exist();
            } catch (Exception $e)
            {

                $this->setDate(strtotime($this->date_) - (24 * 3600));
            }
        } catch (Exception $e)
        {

            $this->bug = true;
        }
    }

    function __destruct() {
        unset($this->db);
    }

    private function exist() {
        $sql = "SELECT daily,dls total,ep FROM stats WHERE date='{$this->date_}'";
        if (!$this->db->query($sql))
        {
            throw new Exception('Aucune statistique trouvée à cette date', 1);
            return false;
        } else
        {
            $this->results = $this->db->getRow(false,false);
            $this->Dstats = unserialize($this->results['daily']);
            $this->Tstats = unserialize($this->results['total']);
            $this->eps = unserialize($this->results['ep']);
            return true;
        }
    }

    function setDate($new_date) {
        if (is_numeric($new_date))
            $this->date_ = date("Y-m-d", $new_date);
        else
            $this->date_ = date("Y-m-d", strtotime($new_date));
        $this->exist();
    }

    function compare($end_d, $daily=false, $path="http://images.gestdown.info/graph/") {
        $first_d = $this->date_;
        if ($daily)
            $f_stats = $this->Dstats;
        else
            $f_stats=$this->Tstats;
        $f_eps = $this->eps;
        try
        {
            $this->setDate($end_d);
        } catch (Exception $e)
        {

            return "Impossible de générer de comparaison avec cette date de fin, essayez un jour avant<br />";
        }

        $compare_s = array();
        $compare_e = array();

        $first_d = date("d-m-y", strtotime($first_d));
        $end_d = date("d-m-y", strtotime($end_d));
        if ($daily)
        {
            foreach ($this->Dstats as $nom => $stat)
            {
                if (isset($f_stats[$nom]))
                    $compare_s[$nom] = $stat - $f_stats[$nom];
                else
                    $compare_s[$nom] = $stat;
            }
            $this->Dstats = $compare_s;
            return $this->daily_display($path, "Comparaison des stats journalière du $first_d avec celles du $end_d ");
        }
        else
        {
            foreach ($this->Tstats as $nom => $stat)
            {
                if (isset($f_stats[$nom]))
                    $compare_s[$nom] = $stat - $f_stats[$nom];
                else
                    $compare_s[$nom] = $stat;

                if (isset($f_eps[$nom]))
                    $compare_e[$nom] = $this->eps[$nom] - $f_eps[$nom];
                else
                    $compare_e[$nom] = $this->eps[$nom];
            }
            $this->Tstats = $compare_s;
            $this->eps = $compare_e;
            return $this->total_display($path, "Statistique du $first_d au $end_d inclus");
        }
    }

    function daily_display($img="http://images.gestdown.info/graph/", $graph_title=NULL) {
        if ($this->bug)
            return "Aucune stats";
        $s_dls = array();
        $s_names = array();
        $output = '';
        foreach ($this->Dstats as $name => $dl)
        {
            $s_names[] = $name;
            $s_dls[] = $dl;
        }
        $total_day = array_sum($s_dls);
        $legend_x = array('Téléchargements');
        $legend_y = $s_names;
        $graph = new CAsBarDiagram;
        $graph->bwidth = 12; // set one bar width, pixels
        $graph->bt_total = 'Total'; // 'totals' column title, if other than 'Totals'
        $graph->showtotals = 0;  // uncomment it if You don't need 'totals' column
        $graph->precision = 0;  // decimal precision
        // call drawing function
        $graph->imgpath = $img;
        if ($graph_title == NULL)
            $graph_title = "Statistiques journalière du " . date("d-m-y", strtotime($this->date_));
        ob_start();
        $graph->DiagramBar($legend_x, $legend_y, array($s_dls), $graph_title);
        echo "<tr class='barhead'><td nowrap><span style=\"color:red;\">Total du jour</span></td>
		
		   <td align=right nowrap>&nbsp; $total_day &nbsp;</td>
		<td align=right nowrap><b>&nbsp;  &nbsp;</b></tr>
		</table><!-- 001 finish -->\n</p>";
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function total_display($img="http://images.gestdown.info/graph/", $graph_title=NULL) {
        if ($this->bug)
            return "Aucune stats";
        $s_dls = array();
        $s_names = array();
        $s_eps = array();
        $output = '';
        foreach ($this->Tstats as $name => $dl)
        {
            $s_names[] = $name;
            $s_dls[] = $dl;
            $s_eps[] = $this->eps[$name];
        }
        $total_day = array_sum($s_dls);
        $total_eps = array_sum($s_eps);
        $legend_x = array('Téléchargements', 'Episodes');
        $legend_y = $s_names;
        $graph = new CAsBarDiagram;
        $graph->bwidth = 8; // set one bar width, pixels
        $graph->bt_total = 'Total'; // 'totals' column title, if other than 'Totals'
        $graph->showtotals = 0;  // uncomment it if You don't need 'totals' column
        $graph->precision = 0;  // decimal precision
        // call drawing function
        $graph->imgpath = $img;
        if ($graph_title == NULL)
            $graph_title = "Statistiques générale au " . date("d-m-y", strtotime($this->date_));
        ob_start();
        $graph->DiagramBar($legend_x, $legend_y, array($s_dls, $s_eps), $graph_title);
        echo "<tr class='barhead'><td nowrap><span style=\"color:red;\">Total général</span></td>
		
		   <td align=right nowrap>&nbsp; $total_day &nbsp;</td>
		   <td align=right nowrap><b>&nbsp; $total_eps  &nbsp;</b></tr>
		   <td align=right nowrap><b>&nbsp;  &nbsp;</b></tr>
		<td align=right nowrap><b>&nbsp;  &nbsp;</b></tr>
		</table><!-- 001 finish -->\n</p>";
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}

?>