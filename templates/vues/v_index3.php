<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo $this->titre; ?></title>
    <meta charset="utf-8">
    <meta name="author" content="www.ame-no-tsuki.fr">
    <meta name="DESCRIPTION" content="<?php echo $this->meta_desc; ?>"/>
    <meta name="KEYWORDS" content="ame no tsuki, mangas, anime, japon, fansub, téléchargement, projets"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="static/css/main.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script type="text/javascript" src="static/js/isotope.pkgd.min.js"></script>
    <script type="text/javascript" src="static/js/main.js"></script>
    <script type="text/javascript" src="static/js/imagesloaded.pkgd.min.js"></script>
    <script type="text/javascript" src="static/js/lazysizes.min.js"></script>
    <script type="text/javascript" src="static/js/series-display.js"></script>
    <meta property="og:title" content="<?php echo $this->titre; ?>" />
    <meta property="og:description" content="<?php echo $this->meta_desc; ?>" />
    <meta name="google-site-verification" content="u5EBaJ0m7q4fc-P3XpHv1qbduymAfNqcEuCJoMJ88kE"/>
    <meta name="msvalidate.01" content="2014CE1E3D3BAD4B6218115A64DBD92F"/>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="static/img/logo.png" title="Gestdown" alt="Gestdown"/></a>
    </div>
    <!-- end logo -->

    <div id="menu_icon"></div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a class="filter selected" data-filter="*" href="#">Toutes les Séries</a></li>
            <li><a class="filter" data-filter=".ongoing" href="#">En Cours</a></li>
            <li><a class="filter" data-filter=".ended" href="#">Terminées</a></li>
            <li><a class="filter" data-filter=".licencie" href="#">Licenciées</a></li>
            <li><a class="filter" data-filter=".abandon" href="#">Abandonnées</a></li>
        </ul>
    </nav>
    <!-- end navigation menu -->

    <div class="footer clearfix">
        <ul class="social clearfix">
            <li><a href="#" class="fb" data-title="Facebook"></a></li>
            <li><a href="#" class="google" data-title="Google +"></a></li>
            <li><a href="#" class="behance" data-title="Behance"></a></li>
            <!--<li><a href="#" class="twitter" data-title="Twitter"></a></li>
            <li><a href="#" class="dribble" data-title="Dribble"></a></li>-->
            <li><a href="#" class="rss" data-title="RSS"></a></li>
        </ul>
        <!-- end social -->

        <div class="rights">
            <p>Copyright © 2014 magnetic.</p>

            <p>Template by <a href="">Pixelhint.com</a></p>
        </div>
        <!-- end rights -->
    </div>
    <!-- end footer -->
</header>
<!-- end header -->

<section class="main clearfix">

    <div id="series">
        <?php
        foreach ($this->series as $serie) {
            $classes = "";
            if ($serie->licencie) {
                $classes .= "licencie ";
            } else if ($serie->finie) {
                $classes .= "ended ";
            } else if ($serie->stopped) {
                $classes .= "abandon ";
            } else {
                $classes .= "ongoing";
            }
            echo <<<EOF
		<div class="work $classes">
			<a href="serie-{$serie->id}-{$this->clean($serie->nom)}.html">
				<img data-sizes="auto" width="$serie->width" height="$serie->height" class="lazyload media" data-src="$serie->image"  alt=""/>
				<div class="caption">
					<div class="work_title">
						<h1 class="name">$serie->nom</h1>
					</div>
				</div>
			</a>
		</div>
EOF;

        }
        ?>
    </div>

</section>
<!-- end main -->
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-9163128-1', 'auto');
    ga('send', 'pageview');

</script>
</body>
</html>