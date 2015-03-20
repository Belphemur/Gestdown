<?php
header("HTTP/1.1 404 Not Found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $this->titre; ?></title>
    <meta charset="utf-8">
    <meta name="author" content="pixelhint.com">
    <meta name="description" content="Magnetic is a stunning responsive HTML5/CSS3 photography/portfolio website  template"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="static/css/main.css">
    <script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php"><img src="static/img/logo.png" title="Gestdown" alt="Gestdown"/></a>
    </div><!-- end logo -->

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
    </nav><!-- end navigation menu -->

    <div class="footer clearfix">
        <ul class="social clearfix">
            <li><a href="#" class="fb" data-title="Facebook"></a></li>
            <li><a href="#" class="google" data-title="Google +"></a></li>
            <li><a href="#" class="behance" data-title="Behance"></a></li>
            <!--<li><a href="#" class="twitter" data-title="Twitter"></a></li>
            <li><a href="#" class="dribble" data-title="Dribble"></a></li>-->
            <li><a href="#" class="rss" data-title="RSS"></a></li>
        </ul><!-- end social -->

        <div class="rights">
            <p>Copyright © 2014 magnetic.</p>
            <p>Template by <a href="">Pixelhint.com</a></p>
        </div><!-- end rights -->
    </div ><!-- end footer -->
</header><!-- end header -->

<section class="main clearfix">

    <section class="top">
        <div class="wrapper content_header clearfix">
            <div class="work_nav">


            </div><!-- end work_nav -->
            <h1 class="title">404 Not Found</h1>
        </div>
    </section><!-- end top -->

    <section class="wrapper">
        <div class="content">
            <p>La série ou l'épisode que vous recherchez n'existe pas/plus.</p>
        </div><!-- end content -->
    </section>
</section><!-- end main -->

</body>
</html>