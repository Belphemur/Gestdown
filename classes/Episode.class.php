<?php
require_once("interfaces/module.interface.php");
require_once("MathCaptcha.class.php");
require_once("Result.class.php");

class Episode implements Module
{
    private $id,$db,$result,$dead,$bbcode, $captcha;
    //Construit "l'épisode" et vérifie qu'avec les info données c'est possible
    function __construct($id,$db)
    {
        $this->debut_t = microtime(true);
        $this->id=$id;
        $this->db=$db;
        $this->dead=false;
        $this->isValid();
        $this->bbcode_activated=false;
        $this->captcha=new MathCaptcha();
    }
    function __destruct()
    {
        unset($this->db);
        unset($this->captcha);
        if($this->bbcode_activated)
            unset($this->bbcode);
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
        $return= new Result();
        $decalage=0;
        $nom=$this->result['nom'];
        $img=$this->result['screen'];
        $date=date("j-m-y à H:i:s",$this->result['date'] + $decalage);
        $auteur=$this->result['auteur'];
        $tele=$this->result['tele'];
        $synopsis=$this->replacement($this->result['synopsis']);

        $width=460;
        if($img=='')
        {
            $img="http://images.gestdown.info/miniatures/{$this->result['cat']}.jpg";
            $width=153;
        }
        $links=array('mq'=>'','hd'=>'','fhd'=>'','tor_mq'=>'','tor_hd'=>'','tor_fhd'=>'');
        $torrent='';

        $template="<label class=\"liens\">Lien %s = </label>
		<a target=\"_blank\" class=\"news_liens_dl\" href=\"dl-{$this->id}-%s.html\">Par ICI</a><br /> \n";

        $tor_template="<label class=\"liens\">Lien %s = </label>
		<a target=\"_blank\" class=\"news_liens_dl\" href=\"tor-{$this->id}-%s.html\">Par ICI</a><br /> \n";

        $dead_template="<a class=\"reporter_lien\" style=\"color:orange;font-size:14px;text-decoration:underline; cursor:pointer;\" >Reporter lien(s) mort</a>
			\n<br /><div id=\"reporter\" class=\"invisible\">
			
			<form action=\"suggestion.php\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" id=\"formDead\" name=\"mort\" target=\"_blank\">
<label class=\"mort\">Calculez : %s</label> <input name=\"math_captcha\" id=\"captchaDead\" type=\"text\" size=\"4\" maxlength=\"3\" /><br />
 <label class=\"mort\">Quel lien pose problème ? :</label>
 <select id=\"quality\" name=\"quality\" size=\"1\">
 <option value=\"mq\">MQ</option>
 <option value=\"hd\">HD</option>
  <option value=\"fhd\">FHD</option>
 <option value=\"Tous\">Tous</option></select><br />
<label class=\"mort\">Explications :</label> <textarea id=\"explication\" name=\"explication\" cols=\"30\" rows=\"5\"></textarea><br />
<input  id=\"mort\" name=\"mort\" type=\"hidden\" value=\"1\" />
<input  id=\"idDead\" name=\"id\" type=\"hidden\" value=\"{$this->id}\" />
<input name=\"\" id=\"Dead\" type=\"button\" value=\"Reporter\" />
</form>
%s
</div><br />
<div id=\"resultD\"></div>";

        if($this->dead)
            $msg_mort='<blink><font style="color:orange;font-size:14px;font-weight:bold;">Un ou plusieurs liens de cet épisode est/sont morts</font></blink>';
        else
        {
            $this->captcha->generateProblem($this->id);
            $calcul=$this->captcha->printProblem(FALSE);
            $hiddenjava=$this->captcha->hiddenJavascript(FALSE,FALSE);
            $msg_mort=sprintf($dead_template,$calcul,$hiddenjava);
        }
        if($synopsis=='')
        {
            $synopsis="<p>Cet épisode n'a pas de synopsis, mais vous pouvez proposer le vôtre : <a class=\"synopsis_link\" style=\"color:orange;font-size:14px;text-decoration:underline; cursor:pointer;\" >Proposez le vôtre</a> <br />
			<div id=\"synopsis_submit\" class=\"invisible\">
			<form action=\"suggestion.php\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" id=\"sugSynops\" name=\"suggestion\" target=\"_blank\">
			<textarea name=\"resume\" id=\"markItUp\" cols=\"80\" rows=\"20\"></textarea><br />
			<label class=\"mort\">Calculez : %s</label> <input name=\"math_captcha\" id=\"captchaSug\" type=\"text\" size=\"4\" maxlength=\"3\" /><br />
			<label class=\"mort\">Votre pseudo : </label> <input name=\"auteur\"  type=\"text\" size=\"15\" maxlength=\"20\" id=\"auteurSug\" /><br />%s
			<label class=\"mort\">Votre screen (facultatif) : </label> <input name=\"screen\"  type=\"text\" size=\"40\" maxlength=\"255\" id=\"screen\"/><br />
			<input id=\"idSug\" name=\"id\" type=\"hidden\" value=\"{$this->id}\" />
			<input name=\"\" id=\"Synops\"type=\"button\" value=\"Proposer\" /></form></div></p><br />
<div id=\"result\"></div>";
            $this->captcha=new MathCaptcha();
            $this->captcha->generateProblem('sug_'.$this->id);
            $calcul=$this->captcha->printProblem(FALSE);
            $hiddenjava=$this->captcha->hiddenJavascript(FALSE,FALSE);
            $synopsis=sprintf($synopsis,$calcul,$hiddenjava);
        }
        else
            $synopsis='<p>'.$synopsis.'</p>';
        if($this->result['mq']!='')
            $links['mq']=sprintf($template,'MQ','mq');
        if($this->result['hd']!='')
            $links['hd']=sprintf($template,'HD','hd');

        if($this->result['fhd']!='')
            $links['fhd']=sprintf($template,'FHD','fhd');

        if($this->result['tor_mq']!='')
            $links['tor_mq']=sprintf($tor_template,'Torrent MQ','mq');

        if($this->result['tor_hd']!='')
            $links['tor_hd']=sprintf($tor_template,'Torrent HD','hd');

        if($this->result['tor_fhd']!='')
            $links['tor_fhd']=sprintf($tor_template,'Torrent FHD','fhd');
        if($this->result['tor_fhd']!='' || $this->result['tor_hd']!='' || $this->result['tor_mq']!='')
            $torrent="<span style=\"font-weight:bold;\">Torrents :</span><br /><br />\n";

        $liens=$links['mq'].$links['hd'].$links['fhd']."<br />\n".$torrent.$links['tor_mq'].$links['tor_hd'].$links['tor_fhd']."<br />\n".$msg_mort;
        $return->nom=$nom;
        $return->img=$img;
        $return->liens=$liens;
        $return->width=$width;
        $return->auteur=$auteur;
        $return->date=$date;
        $return->tele=$tele;
        $return->synopsis=$synopsis;
        $return->exectime=$this->execTime();
        return $return;
    }
}
?>