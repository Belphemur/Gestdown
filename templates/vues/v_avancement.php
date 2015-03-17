	<?php
	$dir='http://images.gestdown.info';
	?>
    <div class="gauche">
	  <div class="droit">
	  <div class="haut">
	   <div>
		<h2>| Avancements</h2></div>
	  </div><!-- /haut -->
	<div id="avancement">
    <?php
	$serie="df";
	$limit=7;
	$i=0;
	foreach($this->avancement as $av)
	{
		$date=$av['date']+21600;//6h de plus du au serveur US
		$date=date("j-m-y à H:i",$date);
		if($serie!=$av['nom'])
		{
			$serie=$av['nom'];
			echo '<div class="separator"><h2>',$serie,'</h2></div>',"\n";
			$i++;
			if($i==$limit)
				$i=0;
		}
		echo '<p><label class="info">Episode ',$av['ep'],'  <span style="font-size:10px">en ',$av['ou'],'</span><br /><span style="font-size:10px"> Modifié le ',$date,'</span></label><img title="Episode ',$av['ep'],' en ',$av['ou'],'"src="',$dir,'/avancement/',$av['ou']=='Pause'?0:$i,'/',$av['ou'],'.png" width="670" height="50" alt="En ',$av['ou'],'" /><br /></p><br />',"\n";
	}
	?>
    
     </div>
	  </div><!-- /droit -->
	 </div><!-- /gauche -->
	  	  <script type="text/javascript">
	  	  _gaq.push(function() {
          var tracker = _gat._getTracker('UA-9163128-1');
          tracker._setDomainName("none");
          tracker._trackPageview("AJAX-avancement.html");
        });
</script>