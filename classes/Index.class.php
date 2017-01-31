<?php
require_once("interfaces/module.interface.php");
require_once("Result.class.php");

class Index implements Module
{
    private $news,$db,$rss,$fichier,$bbcode,$bbcode_activated;
    //Construit la "série" et vérifie qu'avec les info données c'est possible
    function __construct($id,$db)
    {
        $this->debut_t = microtime(true);
        $this->fichier=$id;
        $this->db=$db;
        $this->db->use_disk_cache = false;
        $this->db->cache_queries = false;
        $this->bbcode_activated=false;
        $this->isValid();
    }
    function __destruct()
    {
        unset($this->db);
    }
    function execTime()
    {
        return  round(microtime(true) - $this->debut_t,4);
    }
    function load()
    {
        $f = fopen($this->fichier,"r");
        $data = @fread($f,true ? filesize($this->fichier) : true);
        fclose($f);
        return $data;
    }
    function setBBcode($bbcode)
    {
        $this->bbcode=$bbcode;
        $this->bbcode_activated=true;
    }
    private function replacement($string)
    {
        $text=stripslashes($string);
        if($this->bbcode_activated)
        {
            $text=$this->bbcode->parse($text);
        }
        return $text;
    }

    function isValid()
    {
        $sql="
		(
			SELECT r.date,r.news_id, r.description, r.title, c.nom cat_nom, d.nom dl_nom, d.id dl_id, d.description dl_desc,d.date dl_date
			FROM rss r
			LEFT JOIN downloads d ON d.id = r.dl_id
			RIGHT JOIN categorie c ON d.categorie = c.id
			WHERE r.title != '' AND d.actif=1
		)
		UNION
		(
			SELECT r.date,r.news_id, r.description, r.title, null cat_nom, null dl_nom, r.dl_id dl_id, null dl_desc, null dl_date
			FROM rss r, downloads d, categorie c
			WHERE r.dl_id =0 AND d.actif=1
		)
		ORDER BY dl_date DESC, date DESC
		LIMIT 0,5";
        if(!is_readable($this->fichier))
        {
            throw new GDError('Le fichier de news est inaccessible',1);
            return false;
        }
        else if(!$this->db->mQuery($sql))
        {
            throw new GDError('Aucun résultat pour cette requête',2);
            return false;
        }
        else
        {
            $this->rss=$this->db->getResults(false,false);
            $this->news=$this->load();
            return true;
        }
    }
    //Fonction qui suivant un caneva donné génère le code html pour les épisodes
    //Le caneva doit contenenir les mot suivant : $news,$rss
    function execute()
    {
        $result = new Result();
        $rss_out='';
        $decalage=0;
        $rss_template='<div class="separator">
		<h2>| %s</h2></div>
		<p>%s</p>';
        foreach($this->rss as $news_rss)
        {
            $rss_title="News RSS du ".date("j-m-y à H:i",$news_rss['date']!=0 ? ($news_rss['date']+$decalage) : ($news_rss['dl_date']+$decalage) );
            $epTitre=$news_rss['dl_id']?"({$news_rss['dl_nom']} de {$news_rss['cat_nom']})":"(News consernant le site)";
            $epLiens=$news_rss['dl_id']?"<a href=\"ep-{$news_rss['dl_id']}.html\" title=\"Récupéré les liens de l'épisode\" class=\"news_liens_dl\" target=\"_self\">Cliquer ICI pour les liens</a>":"";
            $rss_news="<u>{$this->replacement($news_rss['title']=='EPISODE'?'Nouvel Episode Sortit' : $news_rss['title'] )} $epTitre</u>\n <br /> {$this->replacement($news_rss['description']==''?$news_rss['dl_desc']:$news_rss['description'])}<br /><br />\n
                    $epLiens<br />\n";
            $rss_out.=sprintf($rss_template,$rss_title,$rss_news);
        }
        $result->news='<p>'.$this->replacement($this->news).'</p>';
        $result->rss=$rss_out;
        $result->exectime=$this->execTime();

        return $result;
    }
    //Renvoie les information d'avancement
    function avancement()
    {
        $return = new Result();
        $sql="SELECT c.nom, a.num ep,a.avancement ou, a.serie, a.date
		FROM avancement a 
		INNER JOIN categorie c 
		ON c.id=a.serie
		ORDER BY c.nom,a.num ASC";
        if($this->db->query($sql))
            $return->avancement=$this->db->getResults(false,false);
        else
            throw new GDError('Aucun épisode en production',10);
        return $return;
    }
}
?>