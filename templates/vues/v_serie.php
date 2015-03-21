<?php
function generateTVEpisode(stdClass $episode, $website) {
    $tvEp = new stdClass();
    $tvEp->{'@context'} = 'http://schema.org';
    $tvEp->{'@type'} = 'TVEpisode';
    $tvEp->episodeNumber= $episode->nombre;
    $tvEp->url = $website . 'ep-'.$episode->id.'.html';
    $tvEp->image = 'https:' . $episode->screen;
    $tvEp->datePublished = date('c', $episode->added);
    $tvEp->name = $episode->titre;
    return $tvEp;
}
    $serie = $this->serie->execute();
    $previousUrl = null;
    $nextUrl = null;
    if(!empty($serie->previous)) {
        $previousUrl = "serie-".$serie->previous->id."-".$this->clean($serie->previous->nom).".html";
    }
    if(!empty($serie->next)) {
        $nextUrl = "serie-".$serie->next->id."-".$this->clean($serie->next->nom).".html";
    }
    $status;
    $statusClass;
    switch($serie->status) {
        case Serie::STATUS_ONGOING:
            $status = 'En Cours';
            $statusClass = "ongoing";
            break;
        case Serie::STATUS_ENDED:
            $status = 'Finie';
            $statusClass = "ended";
            break;
        case Serie::STATUS_ABANDON:
            $status = 'Abandonnée';
            $statusClass = "abandon";
            break;
        case Serie::STATUS_LICENCED:
            $status = 'Licenciée';
            $statusClass = "licenced";
            break;

    }
    $info = $serie->info;
    $screens = "";
    $templateImg = '<img src="%s" />';
    $nbScreen = count($serie->episodes);
    $totalDl =0;
    $TVEpisodes = array();

    $currentEp = null;
    if($nbScreen > 0) {
        for ($i = 0; $i < $nbScreen; $i++) {
            if($i == 0){
                $firstScreen = sprintf($templateImg, $serie->episodes[0]->screen);
            }
            $episode = $serie->episodes[$i];
            if(isset($this->episodeId) && $episode->id == $this->episodeId) {
                $currentEp = $episode;
            }
            $TVEpisodes[] = generateTVEpisode($episode, $this->urlWebsite);
            $totalDl+=$episode->dl;
            $screens .= sprintf($templateImg, $episode->screen) . PHP_EOL;
        }
        $jsonEpisode = json_encode($serie->episodes);
    }


?>
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
    <meta property="og:title" content="<?php echo $this->titre; ?>" />
    <?php
    if(isset($this->episodeId)) {
        ?>
        <meta property="og:image" content="<?php echo 'https:', $currentEp->screen ?>"/>
    <?php
    } else {
        ?>
        <meta property="og:image" content="<?php echo 'https:', $serie->img ?>"/>
    <?php
    }
    ?>
    <meta property="og:description" content="<?php echo htmlentities($serie->synopsis) ?>" />
<?php
if ($nbScreen > 0) {
?>
    <link rel="stylesheet" type="text/css" href="static/css/dataTables.css">
    <link rel="stylesheet" type="text/css" href="static/css/dataTables.responsive.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script type="text/javascript" src="static/js/jquery.cycle2.min.js"></script>
    <script type="text/javascript" language="javascript" src="static/js/datatables.1.10.5.min.js"></script>
    <script type="text/javascript" language="javascript" src="static/js/dataTables.responsive.js"></script>
    <script type="text/javascript" language="javascript" src="static/js/episode-display.js"></script>
<?php
}
?>
    <meta name="google-site-verification" content="u5EBaJ0m7q4fc-P3XpHv1qbduymAfNqcEuCJoMJ88kE"/>
    <meta name="msvalidate.01" content="2014CE1E3D3BAD4B6218115A64DBD92F"/>
    <style media="all" type="text/css">
    .top {
        background:  url('<?php echo $serie->img ?>') no-repeat;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
    }
    </style>
</head>
<body>
<?php
if ($nbScreen > 0) {
    ?>
    <script type="text/plain" id="jsonepisodes">
        <?php echo($jsonEpisode) ?>
    </script>

<?php
}

if(isset($this->episodeId)) {
?>
    <script type="text/plain" id="episodeId"><?php echo $this->episodeId ?></script>
    <script type="application/ld+json">
<?php
    echo json_encode(generateTVEpisode($currentEp, $this->urlWebsite));
?>
    </script>
<?php
} else {
?>
    <script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "TVSeries",
  "author": {
    "@type": "Person",
    "name": <?php echo json_encode(str_replace(array('[', ']'), '', $info->auteur)) ?>

  },
  "name": <?php echo json_encode($serie->nom) ?>,
  "description": <?php echo json_encode($serie->synopsis) ?>,
  "numberOfEpisodes": <?php echo $nbScreen ?>,
  "associatedMedia" : {
    "@type": "ImageObject",
    "contentUrl": "<?php echo 'https:', $serie->img ?>"
  },
  "productionCompany" : {
    "@type": "Organization",
    "name" : <?php echo json_encode(str_replace(array('[', ']'), '', $info->studio)) ?>
  },
  "episodes" : <?php echo json_encode($TVEpisodes) ?>
}

    </script>
<?php
}
?>
<header>
    <div class="logo">
        <a href="index.php"><img src="static/img/logo.png" title="Gestdown" alt="Gestdown"/></a>
    </div>
    <!-- end logo -->

    <div id="menu_icon"></div>
    <nav>
        <ul>
            <li><a class="selected" href="index.php">Home</a></li>
            <li><a class="filter" data-filter="*" href="index.php#filter=*">Toutes les Séries</a></li>
            <li><a class="filter" data-filter=".ongoing" href="index.php#filter=.ongoing">En Cours</a></li>
            <li><a class="filter" data-filter=".ended" href="index.php#filter=.ended">Terminées</a></li>
            <li><a class="filter" data-filter=".licencie" href="index.php#filter=.licencie">Licenciées</a></li>
            <li><a class="filter" data-filter=".abandon" href="index.php#filter=.abandon">Abandonnées</a></li>
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

<section class="main clearfix">

    <section class="top">
        <div class="wrapper content_header clearfix">
            <div class="work_nav">

                <ul class="btn clearfix">
                    <?php if(!empty($previousUrl)) {
                        echo <<<EOF
                    <li><a href="$previousUrl" class="previous" data-title="{$serie->previous->nom}"></a></li>
EOF;
                    }
?>

                    <li><a href="index.php" class="grid" data-title="Series"></a></li>
                    <?php if(!empty($nextUrl)) {
                        echo <<<EOF
                    <li><a href="$nextUrl" class="next" data-title="{$serie->next->nom}"></a></li>
EOF;
                    }
                    ?>
                </ul>

            </div><!-- end work_nav -->
            <h1 class="title"><?php echo $serie->nom ?><p class="status <?php echo $statusClass ?>">(<?php echo $status ?>)</p></h1>
        </div>
    </section><!-- end top -->

    <section class="wrapper">
        <div class="content">

            <p><?php echo $serie->synopsis ?></p>

            <h2>Informations</h2>

            <p>
                <?php
                    echo <<<EOF
                <div class="serieInfo"><label class="info">Année de Production : </label><p class="info">{$info->annee}</p></div>
                <div class="serieInfo"><label class="info">Studio(s) : </label><p class="info">{$info->studio}</p></div>
                <div class="serieInfo"><label class="info">Genre(s) : </label><p class="info">{$info->genre}</p></div>
                <div class="serieInfo"><label class="info">Auteur(s) : </label><p class="info">{$info->auteur}</p></div>
                <div class="serieInfo"><label class="info">Type et Durée : </label><p class="info">{$info->episodes}</p></div>
                (Source : <a href="http://www.animeka.com/" title="Animeka" target="_blank">Animeka</a>)
                <div class="serieInfo"><label class="info">Nombre de téléchargement : </label><p class="info">{$totalDl}</p></div>
EOF;

                ?>
            </p>
            <?php
            if($nbScreen > 0) {
                echo <<<EOF
            <h2>Screenshots</h2>

           <p><div class="cycle-slideshow auto screenshots"
                   data-cycle-fx=scrollHorz
                   data-cycle-timeout=2000
                   data-cycle-caption=".caption"
                   data-cycle-caption-template="{{slideNum}} /  $nbScreen"
                   data-cycle-loader=true
                   data-cycle-progressive="#screenshots"
                >
                $firstScreen
            </div>
            </p>
            <script id="screenshots" type="text/cycle">
               $screens
            </script>
            <h2>Episodes</h2>
            <table id="episodes" class="table table-bordered" width="100%">
              <thead>
                <th>Episode</th>
                <th>id</th>
                <th>Titre</th>
                <th>Téléchargements</th>
                <th>MQ</th>
                <th>HD</th>
                <th>FHD</th>
              </thead>
              <tbody>
              </tbody>
            </table>
EOF;

            } else {
                echo <<<EOF
                <p></p>
EOF;

            }
        ?>
        </div><!-- end content -->
    </section>
</section><!-- end main -->
<?php
if($nbScreen > 0) {
?>
    <script src="//api.peer5.com/peer5.js?id=z142i5n5qypq4cxr" type="application/javascript"></script>
<?php
}
?>
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