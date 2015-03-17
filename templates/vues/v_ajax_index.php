<div class="gauche">
	  <div class="droit">
	  <div class="haut">
	   <div>
		<h2>| Bienvenue</h2></div>
	  </div><!-- /haut -->
	 	<?php echo $this->news;?>
	<br /><br /><a class="faux_lien" id="stats">| Afficher les Stats Totales |</a>&nbsp;&nbsp;&nbsp;<a class="faux_lien" id="d_stats">| Afficher les Stats du Jour |</a><br />
	<div id="daily_stats" class="invisible"><div class="separator">
		<h2>| Statistiques du jour</h2></div>
		<?php echo $this->daily_stats; ?></div>
	<div id="statistiques" class="invisible"><div class="separator">
		<h2>| Statistiques Générale</h2></div>
		<?php echo $this->_stat; ?></div>
		<?php echo $this->rss; ?>
		
	  </div><!-- /droit -->
	 </div><!-- /gauche -->
	  <div class="generate_time">Page générée en <?php echo $this->exectime;?> s</div>
	  	  <script type="text/javascript">
	  	  _gaq.push(function() {
          var tracker = _gat._getTracker('UA-9163128-1');
          tracker._setDomainName("none");
          tracker._trackPageview("AJAX-index.php");
        });
</script>