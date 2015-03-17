		<div class="separator">
		<h2>| Liens de <?php echo $this->nom=='Preview'?'la Preview':'l\''.$this->nom;?></h2></div>
		<p><?php echo $this->liens;?></p>
	<div class="separator">
		<h2>| Informations </h2></div>
		<div class="ep_pic"><img width="<?php echo $this->width; ?>" src="<?php echo $this->img; ?>" alt="Image de l'épisode <?php echo $this->nom;?>" /><br /></div>
		<p><label class="info">Synopsis par  :</label> <?php echo $this->auteur;?><br />
		<label class="info">Mis en ligne le  :</label> <?php echo $this->date;?><br />
		<label class="info">Téléchargé   :</label> <?php echo $this->tele;?> fois<br />
		<label class="info">Episode généré en :</label> <?php echo $this->exectime;?> s<br /></p>
		<div class="separator">
		<h2>| Synopsis </h2></div>
		<?php echo $this->synopsis;?>
	  	  <script type="text/javascript">
	  	  _gaq.push(function() {
          var tracker = _gat._getTracker('UA-9163128-1');
          tracker._setDomainName("none");
          tracker._trackPageview("AJAX-episode_int-<?php echo $this->id;?>.html");
        });
</script>