<?php
    $serie = $this->serie->execute();
    $previousUrl = null;
    $nextUrl = null;
    if(!empty($serie->previous)) {
        $previousUrl = "serie-".$serie->previous->id."-".$this->clean($serie->previous->nom).".html";
    }
    if(!empty($serie->next)) {
        $nextUrl = "serie-".$serie->next->id."-".$this->clean($serie->next->nom).".html";
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo $this->titre; ?></title>
    <meta charset="utf-8">
    <meta name="author" content="pixelhint.com">
    <meta name="DESCRIPTION" content="<?php echo $this->meta_desc; ?>"/>
    <meta name="KEYWORDS" content="ame no tsuki, mangas, anime, japon, fansub, téléchargement, projets"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
    <link rel="stylesheet" type="text/css" href="static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="static/css/main.css">
    <script type="text/javascript" src="static/js/jquery.js"></script>
    <script type="text/javascript" src="static/js/isotope.pkgd.min.js"></script>
    <script type="text/javascript" src="static/js/main.js"></script>
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
            <h1 class="title"><?php echo $serie->nom ?></h1>
        </div>
    </section><!-- end top -->

    <section class="wrapper">
        <div class="content">
            <p><?php echo $serie->synopsis ?></p>


            <h2>Nulla nec pellentesque tempus, ipsum arcu aliquam tortor.</h2>

            <p>vel tempus libero diam vel arcu. Etiam id tincidunt tortor. Nam auctor consequat quam, vel mattis dui laoreet a. Nunc condimentum iaculis tortor, id eleifend nulla mattis lobortis. Pellentesque semper blandit odio, id tempor lorem imperdiet eu. Ut sagittis sagittis consectetur ,Maecenas eget risus eros. Nunc venenatis ante a rutrum cursus.</p>

            <h2>Quisque non semper justo</h2>

            <p>Commodo at blandit vitae, placerat in sem. Morbi ornare nec felis in euismod. Suspendisse vulputate orci ultrices enim facilisis, vel lobortis magna rhoncus. Integer mattis at elit vitae adipiscing. Cras imperdiet cursus nunc quis ullamcorper.</p>

            <p>vel tempus libero diam vel arcu. Etiam id tincidunt tortor. Nam auctor consequat quam, vel mattis dui laoreet a. Nunc condimentum iaculis tortor, id eleifend nulla mattis lobortis. Pellentesque semper blandit odio, id tempor lorem imperdiet eu. Ut sagittis sagittis consectetur ,Maecenas eget risus eros. Nunc venenatis ante a rutrum cursus.</p>


            <p>Commodo at blandit vitae, placerat in sem. Morbi ornare nec felis in euismod. Suspendisse vulputate orci ultrices enim facilisis, vel lobortis magna rhoncus. Integer mattis at elit vitae adipiscing. Cras imperdiet cursus nunc quis ullamcorper.</p>


            <h1>H1 : Quisque non semper justo</h1>
            <h2>H2 : Quisque non semper justo</h2>
            <h3>H3 : Quisque non semper justo</h3>
            <h4>H4 : Quisque non semper justo</h4>
            <h5>H5 : Quisque non semper justo</h5>
            <h6>H6 : Quisque non semper justo</h6>
        </div><!-- end content -->
    </section>
</section><!-- end main -->
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