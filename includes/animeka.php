<?php

function animeka($url)
{
	$result = new Result();
	$include = implode("", file($url));
	
	$fin=strpos($include,"</table></td></tr></table></td></tr><tr><td class=\"animeslegend\">");
	$deb=strpos($include,"PRODUCTION :");
	$page=substr($include,$deb,$fin-$deb);
	$page = preg_replace('#<a[^>]*>(.*?)</a>#', "$1", $page);
	$page=str_replace(" <tr><td class=\"animestxt\">&nbsp;&nbsp;","",$page);
	
	$tab=array();
	$tab=explode("\n", $page);

	if(isset($tab[1]))
	{
		$result->annee=substr($tab[0],13,-10);
		
		$result->studio = utf8_encode(preg_replace('/.*: (.*?)<.*/si','$1', substr($tab[1],7)));
		$result->genre = utf8_encode(preg_replace('/.*: (.*?)<.*/si','$1', substr($tab[2],7)));
		$result->auteurs = utf8_encode(preg_replace('/.*: (.*?)<.*/si','$1', substr($tab[3],7)));
		$result->type= utf8_encode(str_replace("EPS","Episodes de",preg_replace('/.*: (.*?)<.*/si','$1', substr($tab[4],7))));
		$result->display();
	}
	else
		$result->echec=true;
		
	return $result;
}