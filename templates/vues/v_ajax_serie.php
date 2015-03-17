<meta property="og:title" content="<?php echo $this->nom;?> traduit par l'Ame no Tsuki"/>
<meta property="og:image" content="<?php echo $this->img;?>"/>
<div class="gauche">
    <div class="droit">
        <div class="haut">
            <div>
                <h2>| <?php echo $this->nom;?></h2></div>
        </div><!-- /haut -->
        <div id="serie"><div style="text-align:center;"><img src="<?php echo $this->img;?>" alt="Image de la série <?php echo $this->nom;?>" /><br /></div><?php echo $this->stars;?><br />
            <fb:like  show_face="true" width="450" action="like" xid="serie-<?php echo $this->id;?>" title="<?php echo $this->nom;?> traduit par la Ame no Tsuki" href="<?php echo $this->url_site,'serie-',$this->id,'-',strtr($this->nom, ' ', '_');?>.html"></fb:like>
            <div style="text-align: left;>"<a class="news_liens_dl" href="<?php echo $this->url_site,'serie-',$this->id,'-',strtr($this->nom, ' ', '_');?>.html">Lien permanent de la série</a></div>
        </div>
        <div class="separator">
            <h2>| Episodes</h2></div>
        <div id="episode_list">
            <?php echo $this->episode;?></div>
        <div id="episode_info"></div>
    </div><!-- /droit -->
</div><!-- /gauche -->
<div class="generate_time">Série générée en <?php echo $this->exectime;?> s</div>
<script type="text/javascript">
    _gaq.push(function() {
        var tracker = _gat._getTracker('UA-9163128-1');
        tracker._setDomainName("none");
        tracker._trackPageview("AJAX-<?php echo $this->nom;?>.html");
    });
</script>
<div class="invisible" id="prefetch"></div>