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
	// Création du fichier en mémoire 
	$memoryfile = new DOMDocument('1.0', 'utf-8');
	
	// Création du noeud racine
	$root = $memoryfile->createElement('rss'); //On crée l'élément racine
	$root->setAttribute('xmlns:content',"http://purl.org/rss/1.0/modules/content/");
	$root->setAttribute('version', '2.0'); //On lui ajoute l'attribut version (2.0)
	$root = $memoryfile->appendChild($root); //On insère la racine dans le document	
	
	// Création du noeud channel 
	$element_channel = $memoryfile->createElement('channel');//On crée un élément channel
	$element_channel = $root->appendChild($element_channel);//On ajoute cet élément à la racine
	
	// Création du noeud title et ajout du texte à l élément 
	$element_title = $memoryfile->createElement('title', $channel_title);
	$element_channel->appendChild($element_title);//on insère dans le noeud channel
	
	// Création du noeud link et ajout du texte à l élément 
	$element_link = $memoryfile->createElement('link', $channel_link);
	$element_channel->appendChild($element_link);//on insère dans le noeud channel			
	
	// Création du noeud description et ajout du texte à l élément
	$element_description = $memoryfile->createElement('description', $channel_description);
	$element_channel->appendChild($element_description);//on insère dans le noeud channel
	
	//Date de publication
	$element_pub = $memoryfile->createElement('pubDate', date(DATE_RSS, time()));
	$element_channel->appendChild($element_pub);
	
	//Langage
	$element_lang = $memoryfile->createElement('language', 'fr-fr');
	$element_channel->appendChild($element_lang);	
	
	// Création du noeud image et ajout du logo IRD
	$element_image=$memoryfile->createElement('image');
	$element_image=$element_channel->appendChild($element_image);//on insère dans le noeud channel	
	
		// Création du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('url', $channel_img);
		$element_image->appendChild($element_url);//on insère dans le noeud channel	
		
		// Création du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('title', $channel_title);
		$element_image->appendChild($element_url);//on insère dans le noeud channel	
		
		// Création du noeud link et ajout du lien vers le site
		$element_url = $memoryfile->createElement('link', $channel_link);
		$element_image->appendChild($element_url);//on insère dans le noeud channel	
	
	
	//sauvegarde du fichier XML
	saveXML($memoryfile, $filename);
	
	//On retourne le fichier XML
	return openXML($filename);
}


function addOneNews($memoryfile, $title, $description, $link, $timestamp, $author, $guid, $cat)
{
	//ajout d'un élément item fils de channel
	$entries=$memoryfile->getElementsByTagName('channel');
	foreach ($entries as $current_channel)//->item(0);
	{
		$element_channel=$current_channel;
	}
	
	// Création du noeud item
	$element_item = $memoryfile->createElement('item');
	$element_item = $element_channel->appendChild($element_item);	
	
	// Création du noeud title et ajout du texte à l élément 
	$element_title = $memoryfile->createElement('title', $title);
	$element_title = $element_item->appendChild($element_title);
	
	// Création du noeud title et ajout du texte à l élément 
	$element_desc = $memoryfile->createElement('description');
	$element_desc->appendChild($memoryfile->createCDATASection ($description));
	$element_desc = $element_item->appendChild($element_desc);
	
	// Création du noeud link et ajout du texte à l élément 
	$element_link = $memoryfile->createElement('link', $link);
	$element_link = $element_item->appendChild($element_link);
	
	// Création du noeud guid et ajout du texte à l élément 
	$element_guid = $memoryfile->createElement('guid', $guid[1]);
	$element_guid->setAttribute('isPermaLink',$guid[0]);
	$element_guid = $element_item->appendChild($element_guid);
	
	// Création du noeud pubDate et ajout du texte à l élément 
	date_default_timezone_set('Europe/Paris');//définit le fuseau horaire
	$element_date = $memoryfile->createElement('pubDate', date('r', (int)$timestamp));
	$element_date = $element_item->appendChild($element_date);
	
	// Création du noeud author et ajout du texte à l élément 
	$element_author = $memoryfile->createElement('author', $author);
	$element_author = $element_item->appendChild($element_author);
	
	// Création du noeud category et ajout du texte à l élément 
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
	// Création du noeud racine
	$root = $memoryfile->createElement('sorties'); //On crée l'élément racine
	$root->setAttribute('team', 'Ame no Tsuki'); //On lui ajoute l'attribut version (2.0)
	$root = $memoryfile->appendChild($root); //On insère la racine dans le document	
	//sauvegarde du fichier XML
	saveXML($memoryfile, $filename);
	
	//On retourne le fichier XML
	return openXML($filename);
}

function addOneEpisode($memoryfile, $id, $titre, $cat)
{
	//ajout d'un élément item fils de channel
	$entries=$memoryfile->getElementsByTagName('sorties');
	foreach ($entries as $current_channel)//->item(0);
	{
		$element_channel=$current_channel;
	}
	
	// Création du noeud item
	$element_item = $memoryfile->createElement('episode');
	$element_item = $element_channel->appendChild($element_item);	
	
	// Création du noeud id et ajout du texte à l élément 
	$element_id = $memoryfile->createElement('id', $id);
	$element_id = $element_item->appendChild($element_id);
	
	// Création du noeud titre et ajout du texte à l élément 
	$element_titre = $memoryfile->createElement('titre', $titre);
	$element_titre = $element_item->appendChild($element_titre);
	
	// Création du noeud category et ajout du texte à l élément 
	$element_cat = $memoryfile->createElement('serie', $cat);
	$element_cat = $element_item->appendChild($element_cat);
}
?>
