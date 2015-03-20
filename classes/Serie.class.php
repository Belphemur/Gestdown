<?php
require_once("interfaces/module.interface.php");
require_once("Result.class.php");

class Serie implements Module
{
    const STATUS_ONGOING = 1;
    const STATUS_LICENCED = 2;
    const STATUS_ENDED = 3;
    const STATUS_ABANDON = 4;
    private $id, $db, $result, $noEps, $next, $previous;

    /**
     * @param $id
     * @param EzDB $db
     * @param bool $lite
     * @throws Error
     */
    function __construct($id, EzDB $db)
    {
        $this->debut_t = microtime(true);
        $this->id = $id;
        $this->db = $db;
        $this->isValid();
    }

    function __destruct()
    {
        unset($this->db);
    }

    function execTime()
    {
        return round(microtime(true) - $this->debut_t, 4);
    }

    function isValid()
    {
        $sql = "SELECT c.finie, c.licencie, c.stopped, c.nom cat_nom,c.image img, c.description synopsis, d.nom ep, d.nbhits,  d.id, d.description, d.screen, d.lien mq, d.lien2 hd, d.lien3 fhd,
        i.annee, i.auteur, i.episode, i.genre, i.studio
		FROM categorie c
		LEFT JOIN downloads d
		ON c.id=d.categorie
		LEFT JOIN informations i
		ON c.id = i.cat_id
		WHERE c.id=? AND d.actif=1
		ORDER BY d.nom ASC,d.id ASC";

        $sqlNextSerie = "SELECT nom, id FROM categorie WHERE nom > ? AND nom != 'Prob de lien' ORDER BY nom ASC LIMIT 1";
        $sqlPreviousSerie = "SELECT nom, id FROM categorie WHERE nom < ? AND nom != 'Prob de lien' ORDER BY nom DESC LIMIT 1";

        if (!is_numeric($this->id)) {
            throw new Error('L\'identifiant doit être un nombre', 1);
        }

        if (!$this->db->pQuery($sql, array('i', $this->id))) {
            $sql = "SELECT c.finie, c.licencie, c.stopped, c.nom cat_nom,c.image img, c.description synopsis,
        i.annee, i.auteur, i.episode, i.genre, i.studio
		FROM categorie c
		LEFT JOIN informations i
		ON c.id = i.cat_id
		WHERE c.id=?";
            if (!$this->db->pQuery($sql, array('i', $this->id))) {
                throw new Error('Aucun résultat pour cette série', 2);
            } else {
                $this->noEps = true;
                $this->result = $this->db->getResults();
            }
        } else {
            $this->noEps = false;
            $this->result = $this->db->getResults();
        }
        if ($this->db->pQuery($sqlNextSerie, array("s", $this->getNom())))
            $this->next = $this->db->getRow();
        if ($this->db->pQuery($sqlPreviousSerie, array("s", $this->getNom())))
            $this->previous = $this->db->getRow();

        return true;
    }

    /**
     * @return stdClass
     */
    function getInformations()
    {
        $info = new stdClass();
        $info->annee = $this->result[0]->annee;
        $info->auteur = $this->result[0]->auteur;
        $info->episodes = $this->result[0]->episode;
        $info->genre = $this->result[0]->genre;
        $info->studio = $this->result[0]->studio;
        return $info;
    }

    /**
     * @return String
     */
    function getNom()
    {
        return $this->result[0]->cat_nom;
    }

    /**
     * @return String
     */
    function  getImage()
    {
        return $this->result[0]->img;
    }

    /**
     * @return Serie::STATUS_*
     */
    function getStatus()
    {
        $status = Serie::STATUS_ONGOING;
        if ($this->result[0]->licencie) {
            $status = Serie::STATUS_LICENCED;
        } elseif ($this->result[0]->finie) {
            $status = Serie::STATUS_ENDED;
        } elseif ($this->result[0]->stopped) {
            $status = Serie::STATUS_ABANDON;
        }
        return $status;
    }

    /**
     * @return String
     */
    function getSynopsis()
    {
        return stripcslashes($this->result[0]->synopsis);
    }
    //Fonction qui suivant un caneva donné génère le code html pour les épisodes
    //Le caneva doit contenenir les mot suivant : $nom, $img, $episode, $star
    function execute()
    {
        $return = new stdClass();

        $return->nom = $this->getNom();
        $return->img = $this->getImage();
        $return->synopsis = $this->getSynopsis();
        $return->next = $this->next;
        $return->previous = $this->previous;
        $return->status = $this->getStatus();
        $return->info = $this->getInformations();
        $episodes = [];
        if (!$this->noEps) {
            foreach ($this->result as $episode) {
                $ep = new stdClass();
                $ep->id = $episode->id;
                $ep->nombre = $episode->ep;
                $ep->titre = $episode->description;
                $ep->screen = $episode->screen;
                $ep->mq = !empty($episode->mq);
                $ep->hd = !empty($episode->hd);
                $ep->fhd = !empty($episode->fhd);
                $ep->dl = $episode->nbhits;
                $episodes[] = $ep;
            }
        }
        $return->episodes = $episodes;

        $return->exectime = $this->execTime();

        return $return;
    }
}

?>