<?php
function openXML($filename)
{	
	// Ouverture du fichier
	$memoryfile = new DOMDocument();
	$memoryfile->preserveWhiteSpace = false;
	$memoryfile->formatOutput = true;
	$memoryfile->load($filename.'.xml');
	
	//On retourne le fichier
	return $memoryfile;
}

function createNewXML($filename, $channel_title, $channel_description, $channel_link, $channel_img)
{	
	// Cr�ation du fichier en m�moire 
	$memoryfile = new DOMDocument('1.0', 'utf-8');
	
	// Cr�ation du noeud racine
	$root = $memoryfile->createElement('rss'); //On cr�e l'�l�ment racine
	$root->setAttribute('xmlns:content',"http://purl.org/rss/1.0/modules/content/");
	$root->setAttribute('version', '2.0'); //On lui ajoute l'attribut version (2.0)
	$root = $memoryfile->appendChild($root); //On ins�re la racine dans le document	
	
	// Cr�ation du noeud channel 
	$element_channel = $memoryfile->createElement('channel');//On cr�e un �l�ment channel
	$element_channel = $root->appendChild($element_channel);//On ajoute cet �l�ment � la racine
	
	// Cr�ation du noeud title et ajout du texte � l �l�ment 
	$element_title = $memoryfile->createElement('title', $channel_title);
	$element_channel->appendChild($element_title);//on ins�re dans le noeud channel
	
	// Cr�ation du noeud link et ajout du texte � l �l�ment 
	$element_link = $memoryfile->createElement('link', $channel_link);
	$element_channel->appendChild($element_link);//on ins�re dans le noeud channel			
	
	// Cr�ation du noeud description et ajout du texte � l �l�ment
	$element_description = $memoryfile->createElement('description', $channel_description);
	$element_channel->appendChild($element_description);//on ins�re dans le noeud channel
	
	//Date de publication
	$element_pub = $memoryfile->createElement('pubDate', date(DATE_RSS, time()));
	$element_channel->appendChild($element_pub);
	
	//Langage
	$element_lang = $memoryfile->createElement('language', 'fr-fr');
	$element_channel->appendChild($element_lang);	
	
	// Cr�ation du noeud image et ajout du logo IRD
	$element_image=$memoryfile->createElement('image');
	$element_image=$element_channel->appendChild($element_image);//on ins�re dans le noeud channel	
	
		// Cr�ation du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('url', $channel_img);
		$element_image->appendChild($element_url);//on ins�re dans le noeud channel	
		
		// Cr�ation du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('title', $channel_title);
		$element_image->appendChild($element_url);//on ins�re dans le noeud channel	
		
		// Cr�ation du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('link', $channel_link);
		$element_image->appendChild($element_url);//on ins�re dans le noeud channel	
	
	
	//sauvegarde du fichier XML
	saveXML($memoryfile, $filename);
	
	//On retourne le fichier XML
	return openXML($filename);
}


function addOneNews($memoryfile, $title, $description, $link, $timestamp, $author, $guid, $cat)
{
	//ajout d'un �l�ment item fils de channel
	$entries=$memoryfile->getElementsByTagName('channel');
	foreach ($entries as $current_channel)//->item(0);
	{
		$element_channel=$current_channel;
	}
	
	// Cr�ation du noeud item
	$element_item = $memoryfile->createElement('item');
	$element_item = $element_channel->appendChild($element_item);	
	
	// Cr�ation du noeud title et ajout du texte � l �l�ment 
	$element_title = $memoryfile->createElement('title', $title);
	$element_title = $element_item->appendChild($element_title);
	
	// Cr�ation du noeud title et ajout du texte � l �l�ment 
	$element_desc = $memoryfile->createElement('description');
	$element_desc->appendChild($memoryfile->createCDATASection ($description));
	$element_desc = $element_item->appendChild($element_desc);
	
	// Cr�ation du noeud link et ajout du texte � l �l�ment 
	$element_link = $memoryfile->createElement('link', $link);
	$element_link = $element_item->appendChild($element_link);
	
	// Cr�ation du noeud guid et ajout du texte � l �l�ment 
	$element_guid = $memoryfile->createElement('guid', $guid[1]);
	$element_guid->setAttribute('isPermaLink',$guid[0]);
	$element_guid = $element_item->appendChild($element_guid);
	
	// Cr�ation du noeud pubDate et ajout du texte � l �l�ment 
	date_default_timezone_set('Europe/Paris');//d�finit le fuseau horaire
	$element_date = $memoryfile->createElement('pubDate', date('r', (int)$timestamp));
	$element_date = $element_item->appendChild($element_date);
	
	// Cr�ation du noeud author et ajout du texte � l �l�ment 
	$element_author = $memoryfile->createElement('author', $author);
	$element_author = $element_item->appendChild($element_author);
	
	// Cr�ation du noeud category et ajout du texte � l �l�ment 
	$element_cat = $memoryfile->createElement('category', $cat);
	$element_cat = $element_item->appendChild($element_cat);
}

function saveXML($memoryfile, $filename)
{
	//Sauvegarde du fichier
	$memoryfile->save($filename.'.xml');
	
}

function gen_xml($filename)
{
	$memoryfile = new DOMDocument('1.0', 'UTF-8');
	// Cr�ation du noeud racine
	$root = $memoryfile->createElement('sorties'); //On cr�e l'�l�ment racine
	$root->setAttribute('team', 'Ame no Tsuki'); //On lui ajoute l'attribut version (2.0)
	$root = $memoryfile->appendChild($root); //On ins�re la racine dans le document	
	//sauvegarde du fichier XML
	saveXML($memoryfile, $filename);
	
	//On retourne le fichier XML
	return openXML($filename);
}

function addOneEpisode($memoryfile, $id, $titre, $cat)
{
	//ajout d'un �l�ment item fils de channel
	$entries=$memoryfile->getElementsByTagName('sorties');
	foreach ($entries as $current_channel)//->item(0);
	{
		$element_channel=$current_channel;
	}
	
	// Cr�ation du noeud item
	$element_item = $memoryfile->createElement('episode');
	$element_item = $element_channel->appendChild($element_item);	
	
	// Cr�ation du noeud id et ajout du texte � l �l�ment 
	$element_id = $memoryfile->createElement('id', $id);
	$element_id = $element_item->appendChild($element_id);
	
	// Cr�ation du noeud titre et ajout du texte � l �l�ment 
	$element_titre = $memoryfile->createElement('titre', $titre);
	$element_titre = $element_item->appendChild($element_titre);
	
	// Cr�ation du noeud category et ajout du texte � l �l�ment 
	$element_cat = $memoryfile->createElement('serie', $cat);
	$element_cat = $element_item->appendChild($element_cat);
}
?>
