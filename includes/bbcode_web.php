<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2009 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

function unhtmlspecialchars($input) {
	$input = preg_replace("/&gt;/i", ">", $input);
	$input = preg_replace("/&lt;/i", "<", $input);
	$input = preg_replace("/&quot;/i", "\"", $input);
	$input = preg_replace("/&amp;/i", "&", $input);

	return $input;
}
function replace_smileys($text, $calledfrom = 'root'){
	if($calledfrom == 'admin'){
		$prefix = '.';
		$prefix2 = '../';
	}
	else{
		$prefix = '';
		$prefix2 = '';
	}
	
	$filepath = $prefix."./images/smileys/";
	unset($files);
	if ($dh = opendir($filepath)) {
		while($file = readdir($dh)) {
			if (ereg("\.gif",$file)) $files[] = $file;
		}
	}

	$replacements_1 = Array();	
	$replacements_2 = Array();	

	foreach($files as $file) {
		$smiley = explode(".", $file);
		$replacements_1[] = ':'.quotemeta($smiley[0]).':';
		$replacements_2[] = '[SMILE=smile]'.$prefix2.'images/smileys/'.$file.'[/SMILE]';
	}

	$ergebnis = safe_query("SELECT * FROM `".PREFIX."smileys`");
	while($ds = mysql_fetch_array($ergebnis)) {
		$replacements_1[] = $ds['pattern'];
		$replacements_2[] = '[SMILE='.$ds['alt'].']'.$prefix2.'images/smileys/'.$ds['name'].'[/SMILE]';
	}
	
	$text = strtr($text, array_combine($replacements_1, $replacements_2));
	
	return $text;
}
function smileys($text, $specialchars=0, $calledfrom = 'root') {

	if($specialchars) $text=unhtmlspecialchars($text);
	$splits = preg_split("/(\[[\/]{0,1}code\])/si",$text,-1,PREG_SPLIT_DELIM_CAPTURE);
	$anz = count($splits);
	for($i=0;$i<$anz;$i++){
		$opentags = 0;
		$closetags = 0;
		$match = false;
		if(strtolower($splits[$i]) == "[code]"){
			$opentags++;
			for($z=($i+1);$z<$anz;$z++){
				if(strtolower($splits[$z]) == "[code]") $opentags++;
				if(strtolower($splits[$z]) == "[/code]") $closetags++;
				if($closetags == $opentags){
					$match = true;
					break;
				}
			}
		}
		if($match == false){
			$splits[$i] = replace_smileys($splits[$i], $calledfrom);
		}
		else {
			$i = $z;			
		}		
	}
	$text = implode("",$splits);
	if($specialchars) $text=htmlspecialchars($text);
	return $text;
}

function cut_middle($str, $max = 50 ){
 	$strlen = mb_strlen($str);
	if( $strlen>$max ){
		$part1 = mb_substr($str,0,$strlen/2);
		$part2 = mb_substr($str,$strlen/2);
		$part1 = mb_substr($part1,0,($max/2)-3)."...";
		$part2 = mb_substr($part2,-($max/2));
		$str = $part1.$part2;
	}
	return $str;
}


function htmlnl($text){
	preg_match_all('/<(table|li|ul|ol|tr|td|dl|dt|dd|dir|menu|th|thead|caption|colgroup|col|tbody|tfoot*)[^>]*>(.*?)<\/\1>/si',$text,$matches,PREG_SET_ORDER);
	foreach($matches as $match){
		if(stristr($match[0],'class="quote"') === false && stristr($match[0],'class="code"') === false){
			$new_str = str_replace(array("\r\n", "\n", "\r"),array("", "", ""),$match[0]);
			$text = str_replace($match[0],$new_str,$text);
		}
	}
	return $text;
}

function fixJavaEvents($string){
	return str_replace(array('onabort=', 'onblur=', 'onchange=', 'onclick=', 'ondblclick=', 'onerror=', 'onfocus=', 'onkeydown=', 'onkeypress=', 'onkeyup=', 'onload=', 'onmousedown=', 'onmousemove=', 'onmouseout=', 'onmouseover=', 'onmouseup=', 'onreset=', 'onresize=', 'onselect=', 'onsubmit=', 'onunload=', ' '),'',$string);
}

function flags($text,$calledfrom = 'root') {
  global $_language;

	
	if($calledfrom == 'admin'){
		$prefix = '../';
	}
	else{
		$prefix = '';
	}
	$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries`");
	while($ds = mysql_fetch_array($ergebnis)) {
		$text = str_ireplace ("[flag]".$ds['short']."[/flag]", '<img src="'.$prefix.'images/flags/'.$ds['short'].'.gif" width="18" height="12" border="0" alt="'.$ds['country'].'" />', $text);
	}

	$text = str_ireplace ("[flag][/flag]", '<img src="'.$prefix.'images/flags/na.gif" width="18" height="12" border="0" alt="'.$_language->module['na'].'" />', $text);
	$text = str_ireplace ("[flag]", '', $text);
	$text = str_ireplace ("[/flag]", '', $text);

	return $text;
}

//replace [code]-tags



//replace [img]-tags

function imgreplace($content) {
	global $_language;

	global $picsize_l;
	global $picsize_h;
	global $autoresize;

	if($autoresize>0) {
		preg_match_all("#(\[img\])(.*?)(\[\/img\])#i", $content, $imgtags, PREG_SET_ORDER);
		$i=0;
		foreach($imgtags as $teil) {
			$i++;
			if($autoresize == 1) {
				$picinfo = getimagesize($teil[2]);
				switch($picinfo[2]) {
					case 1: $format = "gif"; break;
					case 2: $format = "jpeg"; break;
				}
				if(!$picsize_l) $size_l = "9999"; else $size_l=$picsize_l;
				if(!$picsize_h) $size_h = "9999"; else $size_h=$picsize_h;
				if($picinfo[0] > $size_l OR $picinfo[1] > $size_h) 
				$content = str_ireplace('[img]'.$teil[2].'[/img]', '[url='.$teil[2].']<img src="'.fixJavaEvents($teil[2]).'" border="0" width="'.$picsize_l.'" alt="'.$teil[2].'" /><br />([i]'.$_language->module['auto_resize'].': '.$picinfo[1].'x'.$picinfo[0].'px, '.$format.'[/i])[/url]', $content);
				elseif($picinfo[0] > (2*$size_l) OR $picinfo[1] > (2*$size_h)) $content = str_ireplace('[img]'.$teil[2].'[/img]', '[url='.$teil[2].'][b]'.$_language->module['large_picture'].'[/b]<br />('.$picinfo[1].'x'.$picinfo[0].'px, '.$format.')[/url]', $content);
				else $content = preg_replace('#\[img\]'.preg_quote($teil[2],"#").'\[/img\]#si', '<img src="'.fixJavaEvents($teil[2]).'" border="0" alt="'.$teil[2].'" />', $content, 1);
			}
			else {
				$n = str_replace('.', '', microtime(1)).'_'.$i;
				$n = str_replace(' ', '', $n);
				$content = preg_replace('#\[img\]'.preg_quote($teil[2],"#").'\[/img\]#si', '<img src="'.fixJavaEvents($teil[2]).'" id="ws_image_'.$n.'" border="0" onload="checkSize(\''.$n.'\', '.$picsize_l.', '.$picsize_h.')" alt="'.fixJavaEvents($teil[2]).'" style="max-width: '.($picsize_l+1).'px; max-height: '.($picsize_h+1).'px;" /><div id="ws_imagediv_'.$n.'" style="display:none;">[url='.fixJavaEvents($teil[2]).'][i]('.$_language->module['auto_resize'].': '.$_language->module['show_original'].')[/i][/url]</div>', $content, 1);
			}
		}
	}
	else $content = preg_replace("#\[img\](.*?)\[/img\]#sie", "'<img src=\"'.fixJavaEvents('\\1').'\" border=\"0\" alt=\"'.fixJavaEvents('\\1').'\" />'", $content);

	return $content;
}

//replace [quote]-tags

function quotereplace($content) {
  
	global $_language, $picsize_l;

	$border='';
	$bg1='';

	$content = str_ireplace('[quote]', '[quote]', $content);
	$content = str_ireplace('[/quote]', '[/quote]', $content);
	$wrote = 'a écrit ';
	$content = preg_replace("#\[quote=(.*?)\]#si", "[quote][b]\\1 ".$wrote.":[/b][br][hr]",$content);

	//prepare: how often start- and end-tag occurrs
	$starttags = substr_count($content, '[quote]');
	$endtags = substr_count($content, '[/quote]');

	$overflow=abs($starttags-$endtags);

	for($i=0;$i<$overflow;$i++) {
		if($starttags>$endtags) $content=$content.'[/quote]';
		elseif($endtags>$starttags) $content='[quote]'.$content;
	}

	$content = preg_replace("#\[quote\]#s", '<div style="width:'.$picsize_l.'px;height:100%;overflow:auto;background-color:'.$bg1.';border: 1px '.$border.' solid;" class="quote">', $content, 10);
	$content = preg_replace("#\[/quote\]#s", '</div>', $content, 10);

	//remove overflowed quote-tags

//	$content=eregi_replace('\[quote\]','',$content);
//	$content=eregi_replace('\[/quote\]','',$content);

	return $content;

}



function urlreplace($content){	
 	$starttags = substr_count(strtolower($content), strtolower('[url'));
	$endtags = substr_count(strtolower($content), strtolower('[/url]'));
	$overflow=abs($starttags-$endtags);
	for($i=0;$i<$overflow;$i++) {
		if($starttags>$endtags) $content=$content.'[/url]';
		elseif($endtags>$starttags) $content='[url]'.$content;
	}
	$content = preg_replace("#\[url\](.*?)\[/url\]#i","[url=\\1]\\1[/url]",$content);
	preg_match_all("/\[url=(\[(.*?)\])\]/si",$content,$erg,PREG_SET_ORDER);
	foreach($erg as $match){
		preg_match("/\[(.*?)\](.*?)\[(.*?)\]/si",$match[1],$new_erg);
		$match_rep = str_replace($match[1],$new_erg[2],$match[0]);
		$content = str_replace($match[0],$match_rep,$content);
	}
	$content = preg_replace("#\[url=(.*?)\]#ie","'<a href=\"'.fixJavaEvents('\\1').'\" target=\"_blank\">'",$content);
	$content = preg_replace("#\<a href='www(.*?)' target='_blank'>#i","<a href='http://www\\1' target='_blank'>",$content);
	$content = str_ireplace("[/url]","</a>",$content);
	return $content;
}

function linkreplace($link){
	if( ord($link[1])==39 || ord($link[1])==62 ) return $link[0];
	else{
		$backup = "";
		$backup_end = "";
		if(mb_substr($link[0],1,1) == "]"){
			$backup = mb_substr($link[0],0,2);
			$link[0] = mb_substr($link[0],2);
			$link[0] = mb_substr($link[0],0,mb_strrpos($link[0],"["));
			$backup_end = mb_substr($link[3],mb_strrpos($link[3],"["));
			$link[3] = mb_substr($link[3],0,mb_strrpos($link[3],"["));
		}
	 	if(preg_match("%(http://|https://|ftp://|mailto:|news:|www.)([a-zA-Z0-9-\.]{3,50})(\.[a-z]{2,4})%s",$link[0])){
		 	$http = $link[2];
		 	if(mb_substr($http,0,4)=="www."){
				$http = "http://".$http;
			}
			$link = str_replace(trim($link[0]),'<a href="'.$http.$link[3].'" target="_blank" rel="nofollow">'.$link[2].$link[3].'</a>',$link[0]);
			return $backup.$link.$backup_end;
		}
		return $backup.$link[0].$backup_end;
	}
}

function cut_urls($link){
 	$new_str = $link[1];
 	if(!stristr($link[1],"<img") && !stristr($link[1],"[SMILE")){
		$new_str = cut_middle($link[1]);
	}
	$link[0] = ( stristr($link[0],"javascript:") ) ? str_ireplace("javascript:","#killed",$link[0]) : $link[0];
	return str_replace(">".$link[1],">".$new_str,$link[0]);
}


function replacement($content, $bbcode=true) {
		$content = htmlentities($content);
		$content = imgreplace($content);
		$content = quotereplace($content);
		$content = urlreplace($content);
		$content = preg_replace_callback("#(^|<[^\"=]{1}>|\s|\[b|i|u\]][^<a.*>])(http://|https://|ftp://|mailto:|news:|www.)([^\s<>|$]+)#si","linkreplace",$content);
		$content = preg_replace("#\[email\](.*?)\[/email\]#sie", "'<a href=\"mailto:'.mail_protect(fixJavaEvents('\\1')).'\">'.fixJavaEvents('\\1').'</a>'", $content);
		$content = preg_replace("#\[email=(.*?)\](.*?)\[/email\]#sie", "'<a href=\"mailto:'.mail_protect(fixJavaEvents('\\1')).'\">\\2</a>'", $content);
		$content = preg_replace_callback("#<a\b[^>]*>(.*?)</a>#si","cut_urls",$content);
		//while(preg_match("#\[size=(.*?)\](.*?)\[/size\]#si", $content)){
		  $content = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", "<font size=\"\\1\">\\2</font>", $content);
		//}
		//while(preg_match("#\[color=(.*?)\](.*?)\[/color\]#si", $content)){  
		  $content = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", "<font color=\"\\1\">\\2</font>", $content);
		//}
	
		 
		//while(preg_match("#\[bgcolor=(.*?)\](.*?)\[/bgcolor\]#si", $content)){  
		  $content = preg_replace("#\[bgcolor=(.*?)\](.*?)\[/bgcolor\]#si", "<span style=\"background-color :\\1\";>\\2</span>", $content);
		//}
		//while(preg_match("#\[font=(.*?)\](.*?)\[/font\]#si", $content)){
		  $content = preg_replace("#\[font=(.*?)\](.*?)\[/font\]#si", "<font face=\"\\1\">\\2</font>", $content);
		//}
		//while(preg_match("#\[align=(.*?)\](.*?)\[/align\]#si", $content)){
		  $content = preg_replace("#\[align=(.*?)\](.*?)\[/align\]#si", "<div align=\"\\1\">\\2</div>", $content);
		//}
		
		$content = preg_replace("#\[list=(.*?)\](.*?)\[/list\]#si", "<ul type=\"\\1\">\\2</ul>", $content);
		$content = preg_replace("#\[list](.*?)\[/list\]#si", "<ul>\\1</ul>", $content);
		
		$content = preg_replace("#\[\*\]([^\n]*?)\n#sU", "<li>\\1</li>",$content);

		$content = preg_replace("#\[b\](.*?)\[/b\]#si", "<b>\\1</b>",$content);
		$content = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>",$content);
		$content = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>",$content);
		$content = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>",$content);
		$content = preg_replace("#\[br]#si", "<br />", $content);
		$content = preg_replace("#\[hr]#si", "<hr />", $content);
		$content = preg_replace("#\[center]#si", "<center>", $content);
		$content = preg_replace("#\[/center]#si", "</center>", $content);
		$content = str_replace('\n','<br />',$content);
		$content = nl2br($content);
		$content = stripslashes($content);
		
	return $content;
}
?>
