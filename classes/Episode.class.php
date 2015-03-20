<?php
require_once("interfaces/module.interface.php");
require_once("MathCaptcha.class.php");
require_once("Result.class.php");

class Episode implements Module
{
    private $id,$db,$result;
    //Construit "l'épisode" et vérifie qu'avec les info données c'est possible
    function __construct($id,ezDB $db)
    {
        $this->debut_t = microtime(true);
        $this->id=$id;
        $this->db=$db;
        $this->dead=false;
        $this->isValid();
    }
    function __destruct()
    {
        unset($this->db);
    }
    function isValid()
    {
        $sql="SELECT d.nom,d.nbhits tele,d.auteur,d.date, d.description synopsis,d.id,d.lien mq,d.lien2 hd,d.lien3 fhd,d.torrentMQ tor_mq,d.torrentHD tor_hd,d.torrentFHD tor_fhd,d.screen,c.nom cat,d.mort,d.categorie
		FROM downloads d
		INNER JOIN categorie c
		ON c.id=d.categorie
		WHERE d.id=? AND d.actif=1 ";
        if(!is_numeric($this->id))
        {
            throw new Error('L\'identifiant doit être un nombre',1);
            return false;
        }
        else if(! $this->db->pQuery($sql,array('i',$this->id)))
        {
            throw new Error('Aucun résultat pour cet épisode',2);
            return false;
        }
        else
        {
            $this->result=$this->db->getRow(false,false);
            $this->dead=$this->result['mort'];
            return true;
        }
    }
    function execTime()
    {
        return  round(microtime(true) - $this->debut_t,4);
    }
    function getNom()
    {
        return $this->result['nom']." de ". $this->result['cat'];
    }
    function getCatId()
    {
        return $this->result['categorie'];
    }
    //Fonction qui suivant un caneva donné génère le code html pour l'épisode
    //Le caneva doit contenir les mot suivant : $nom, $img, $liens, $width,$synopsis,$tele,$auteur,$mort

    function execute()
    {
      throw new Error("Not implemented");
    }
}
?>