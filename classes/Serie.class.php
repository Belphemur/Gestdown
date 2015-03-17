<?php
require_once("interfaces/module.interface.php");
require_once("Result.class.php");
class Serie implements Module
{
    private $id,$db,$result,$lite, $noEps;
    //Construit la "série" et vérifie qu'avec les info données c'est possible
    function __construct($id,$db,$lite=false)
    {
        $this->debut_t = microtime(true);
        $this->id=$id;
        $this->db=$db;
        $this->lite=$lite;
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
    function isValid()
    {
        $sql="SELECT c.nom cat_nom,c.image img, d.nom ep, d.id
		FROM downloads d
		INNER JOIN categorie c
		ON c.id=d.categorie
		WHERE d.categorie=? AND d.actif=1
		ORDER BY d.id";
        if($this->lite)
            $sql="SELECT c.nom cat_nom, d.nom ep, d.id
			FROM downloads d
			INNER JOIN categorie c
			ON c.id=d.categorie
			WHERE d.categorie=?
			ORDER BY d.id";	
        if(!is_numeric($this->id))
        {
            throw new Error('L\'identifiant doit être un nombre',1);
            return false;
        }
        else if(! $this->db->pQuery($sql,array('i',$this->id)))
        {
            $sql="SELECT c.nom cat_nom,c.image img
		FROM categorie c
		WHERE c.id=?";
            if(! $this->db->pQuery($sql,array('i',$this->id)))
            {
                throw new Error('Aucun résultat pour cette série',2);
                return false;
            }
            else
            {
                $this->noEps=true;
                $this->result=$this->db->getResults(false,false);
                return true;
            }
        }
        else
        {
            $this->noEps=false;
            $this->result=$this->db->getResults(false,false);
            return true;
        }
    }
    function getNom()
    {
        return $this->result[0]['cat_nom'];
    }
    //Fonction qui suivant un caneva donné génère le code html pour les épisodes
    //Le caneva doit contenenir les mot suivant : $nom, $img, $episode, $star
    function execute()
    {
        $return=new Result();

        if(!$this->lite)
        {
            $return->nom=$this->result[0]['cat_nom'];
            $return->img=$this->result[0]['img'];
            $ratingManager = RatingManager::getInstance();
            $return->stars=$ratingManager->drawStars($this->id);
        }
        else
            $return->catnom=$this->result[0]['cat_nom'];
        if(!$this->noEps)
        {
            $output="";
            $template="<a href=\"ep-%d.html\" class=\"\" id=\"%d\"><div class=\"ep\">%s</div></a>\n";
            $compteur=0;

            foreach($this->result as $episode =>$ep)
            {
                if($compteur==4)
                {
                    $output.= sprintf($template,$ep['id'],$ep['id'],$ep['ep']).'<br />';
                    $compteur=0;
                }
                else
                {
                    $output.= sprintf($template,$ep['id'],$ep['id'],$ep['ep']);
                    $compteur++;
                }
            }
            $return->episode=$output;
        }
        else
            $return->episode="Aucun pour le moment.";

        if(!$this->lite)
            $return->exectime=$this->execTime();
        else
            $return->catexectime=$this->execTime();

        return $return;
    }
}
?>