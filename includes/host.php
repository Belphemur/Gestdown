<?php
function nom($host)
{
    $nom=trim(filter_nom($host));

    if(strpos($nom,'ghost') or strpos($nom,'Ghost'))
        $ret='07-Ghost';
    if(strpos($nom,'eyes') or strpos($nom,'Eyes'))
        $ret='11eyes';
    else if(strpos($nom,'jigoku'))
        $ret="Pandora Hearts";
    else if(strpos($nom,'Guin'))
        $ret="Guin Saga";
    else if(strpos($nom,'HOLiC'))
        $ret="xXx HOLiC Shunmuki";
    else
        $ret=$nom;
    return trim($ret);
}

function filter_nom($in)
{

    $search = array ('%20','%5B','amp;','%5D','mp','html','AnT','FHD','MQ','HD','FMA','avi','JNF','JnF','jnf','ANT','ant','mq','fhd','hd','download','mirrors','OAD','class=lien_news','END','end','FIN','fin','V2','v2','mirror-');
    $in = preg_replace('/_-(.*)-mp4/si', '', $in);
    $replace = array (' ','');
    $rep=str_replace($search, $replace, $in);

    return preg_replace('@[^a-zA-Z]@i',' ',$rep);
}
function num($in)
{
    $search = array ('07_Ghost','07ghost','07-Ghost','%20','mp4','%5B','%5D','11eyes','v2','V2');
    $in = preg_replace('/_-(.*)-mp4/si', '', $in);
    $replace = array ('');
    $rep=str_replace($search, $replace, $in);
    return intval(preg_replace('@[^0-9]@i','', $rep));
}
function host($lien)
{
    if(strpos($lien,"mirorii") || strpos($lien,"miroriii")|| strpos($lien,"imagidream") ||strpos($lien,"hotfile")||strpos($lien,"rapidshare"))
    {
        $host=strstr($lien,"AnT");
    }
    else if(strpos($lien,"gestdown"))
    {
        $host=strstr($lien,"mirror");
    }
    else
        $host=strstr($lien,"ant");

    return $host;
}
function linkInformations($in,$qual=false)
{
    $matches;
    $serie;
    $num;
    $quality;
    if( preg_match('/\[.+\](.+)_([0-9]*|\d*\.\d{1}?\d*|OAV|OAD)_(HD|MQ|FHD)(|.+)\.(mp4|avi|mkv)/', $in,$matches))
    {
        $serie=preg_replace('@[^a-zA-Z0-9-.!]@i',' ',$matches[1]);
        $num= $matches[2];
        if($qual)
            $quality=$matches[3];
    }
    else
    {
        $serie=nom($in);
        $num=num($in);
    }
    if($qual)
        return array($num,$serie,$quality);
    else
        return array($num,$serie);
}

?>