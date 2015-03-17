<?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start(); ?>
<?php
require_once("ajax_index.php");
$imgUrl='http://images'.$urlPattern;
$minUrl='http://min'.$urlPattern;
$vue=new View("./templates/vues/");
$imageVide="miniatures/Aucune.jpg";
$titre="Ame no Tsuki [AnT] (Gestdown) : ";
$meta_desc="Site centralisant les épisode de la Ame No Tsuki qui est une team de fansub. Le site contient tous les liens de la team, les épisodes, un forum et plein d'autre informations. Notre boulot consiste à vous faire découvrir la japanimation, nous traduisons et sous-titrons plusieurs séries d'animations japonaises.";

if(isset($_GET['id']))
	$id=$_GET['id'];
$synopsis="";
$header_js="";
if(isset($_GET['ext']))
{
	if(isset($_GET['ep']) )
	{
		$result=episode($id,true);
		$synopsis=$result->html;
		$meta_desc=$result->nom." traduit par la Ame no Tsuki [AnT]";
		$titre.=$result->nom;
		$id=$result->catId;
		$header_js="
		<script type=\"text/javascript\">
		$(document).ready(function(){
		var cat =[];
		var nom=[];
		$('#class_rotator_classes img').each(function(i)
		{
		cat[$(this).attr('rel')] =[i+1] ;
		})
		var id= $id ;
		moveRotator(cat[id]);
		$('#markItUp').markItUp(mySettings);
		});
		</script>";
	}
	else if(isset($_GET['serie']))
		{
			$header_js="
					<script type=\"text/javascript\">
					$(document).ready(function(){
					var cat =[];
					var nom=[];
					$('#class_rotator_classes img').each(function(i)
					{
					cat[$(this).attr('rel')] =[i+1] ;
					})
					var id= $id ;
					moveRotator(cat[id]);
					            window.fbAsyncInit = function() {
                FB.init({
                    appId: '120961717939204',
                    status: true,
                    cookie: true,
                    xfbml: true
                });
            };
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                '//connect.facebook.net/fr_FR/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());
					});
					</script>";
			$result=serie($id);
			$synopsis=$result->html;
			$meta_desc=$result->synopsis;
			$titre.="Série -  ".$result->nom;
			
		}
	else if(isset($_GET['avancement']))
		{
		$header_js="
				<script type=\"text/javascript\">
				$(document).ready(function(){
				var cat =[];
				var nom=[];
				$('#class_rotator_classes img').each(function(i)
				{
				cat[$(this).attr('rel')] =[i+1] ;
				})
				var id= -1 ;
				moveRotator(cat[id]);
				
				});
				</script>";
		$result=index(true);
		$synopsis=$result->html;
		$titre.="Avancement des projets de la Ame no Tsuki [AnT]";
		
	}
}
else
{
	$header_js="
			<script type=\"text/javascript\">
			$(document).ready(function(){
			moveRotator(1);
			
			});
			</script>";
	$synopsis=index();
	$titre="Gestdown : Centralisation des épisodes de la Ame no Tsuki [AnT]";
}

$cours='';
$finies="";
$img='<img rel="0"  title="index" class="" style="width: 80px; display: block; height: 103px; margin-top: 47px;" src="'.$minUrl.'index.jpg" id="class_rotator_1" alt="Miniature de l\'index" />'."\n";
$img.='<img rel="-1"  title="avancement" class="" style="width: 80px; display: block; height: 103px; margin-top: 47px;" src="'.$minUrl.'avancement.jpg" id="class_rotator_2" alt="Miniature de l\'avancement" />'."\n";
$i=3;
$stop='';
$datas=$db->getResults("SELECT id,nom,finie,stopped FROM categorie WHERE nom!='Prob de lien' AND licencie!=1 ORDER BY stopped ASC,finie ASC,nom ASC",false);
$meta_tag_serie="";

foreach ($datas as $serie)
{
	$meta_tag_serie.=$serie['nom'].', ';
	if($serie['finie'])
		$finies.="<a href=\"".$url_site."serie-".$serie['id']."-".strtr($serie['nom'], ' ', '_').".html\" rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />\n";
	else if($serie['stopped'])
            $stop.="<a href=\"".$url_site."serie-".$serie['id']."-".strtr($serie['nom'], ' ', '_').".html\" rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />\n";
        else
		$cours.="<a href=\"".$url_site."serie-".$serie['id']."-".strtr($serie['nom'], ' ', '_').".html\" rel='".$serie['id']."' title='".$serie['nom']."' class=\"\" id=\"class_nav_".$i."\">".$serie['nom']."</a><br />\n";
	$img.='<img rel="'.$serie['id'].'"  title="'.$serie['nom'].'" class="" style="width: 80px; display: block; height: 103px; margin-top: 47px;" src="'.$minUrl.$serie['nom'].'.jpg" id="class_rotator_'.$i.'" alt="Miniature de la série '.$serie['nom'].'" onError="this.src=\''.$imgUrl.$imageVide.'\'"/>
			';
	$i++;
}
$meta_tag_serie.='action';

$vue->jsUrl='http://www'.$urlPattern.'js/';
$vue->imgUrl=$imgUrl;
$vue->cssUrl='http://www'.$urlPattern.'css/';
$vue->styleUrl='http://www'.$urlPattern.'style/';
$vue->admin=$admin;
$vue->finies=$finies;
$vue->cours=$cours;
$vue->stop=$stop;
$vue->img=$img;
$vue->meta_tag_serie=$meta_tag_serie;
$vue->header_js=$header_js;
$vue->titre=$titre;
$vue->meta_desc=$meta_desc;
$vue->synopsis=$synopsis;

echo $vue->render("v_index2.php");
unset($GLOBALS['db']);
?>
