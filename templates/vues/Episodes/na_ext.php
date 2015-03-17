	<div class="gauche">
	  <div class="droit">
	  <div class="haut">
	   <div>
		<h2>| <?php echo $this->catnom;?></h2></div>
	  </div><!-- /haut -->
		<div class="separator">
		<h2>| Episodes</h2></div>
	   <div id="episode_list">
	   <?php echo $this->episode;?></div>
	   <div id="episode_info"> <div class="separator"><h2>| Liens de <?php echo $this->nom=='Preview'?'la Preview':'l\''.$this->nom;?></h2></div>
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
	  </div><!-- /droit -->
	 </div><!-- /gauche -->
	</div>
	  <div class="generate_time">Série générée en <?php echo $this->catexectime;?> s</div>
	  	  <script type="text/javascript">
	  	  _gaq.push(function() {
          var tracker = _gat._getTracker('UA-9163128-1');
          tracker._setDomainName("none");
          tracker._trackPageview("AJAX-<?php echo $this->catnom;?>-<?php echo $this->nom=='Preview'?'la Preview':'l\''.$this->nom;?>.html");
        });
</script></div>
<div class="invisible" id="prefetch"></div>