/*************************************************************************
 * verification_formulaire.js
 * Copyright (c) François Pirsch 2007
 * http://aspirine.org/contact/
 * Distribué sous licence BSD.
 *
 *  30 mars 2007
 *  30 juin 2007 (internationalisation)
 *  30 juin 2008 (adaptation pour les <fieldset>, <legend> et autres éléments sans nom)
 *  12 août 2008 (utilisation en tant qu'objet)
 *  15 août 2008 (correction d'un bug)
 *  17 août 2008 (correction d'un bug pour les <select> sous Internet Explorer)
 *
 * Vérifie automatiquement la validité d'un formulaire avant envoi.
 * Installation :
 * 1. Copier ce fichier sur le site
 * 2. Le lier au fichier HTML contenant le formulaire :
 *   <script type="text/javascript" src="verification_formulaire.js"></script>
 * 3. Mettre onsubmit="return valideForm(this)" au formulaire à vérifier.
 *
 * Dans le formulaire :
 * - L'élément contenant l'adresse email doit s'appeler "email"
 * - S'il y a un champ de confirmation d'adresse email,
 *   il doit s'appeler "email2"
 * - Les champs obligatoires doivent être précédés par le caractère "*".
 *************************************************************************/

function valideForm(f) {
	var champsObligatoires = [];
	var adresse1;
	var erreurs = [];

	// Détection de la langue du navigateur
	var lang = (navigator.language || navigator.userLanguage).substr(0, 2).toLowerCase();

	// Messages français
	var msg_fr = new Array();
	msg_fr["toolong"] = "Cette adresse est trop longue";
	msg_fr["invalid"] = "L'adresse que vous avez entrée est invalide.";
	msg_fr["mailrequired"] = "Veuillez entrer votre adresse email.";
	msg_fr["different"] = "Il y a une faute de frappe entre les deux adresses entrées.";
	msg_fr["confirm"] = "La confirmation d'adresse email est obligatoire.";
	msg_fr["required1"] = 'Le champ "';
	msg_fr["required2"] = '" est obligatoire.';
	msg_fr["bracketsno"] = " n'est ni de type checkbox ni de type select-multiple, son nom ne doit pas se terminer par []";
	msg_fr["bracketsyes"] = " et peut prendre plusieurs valeurs, son nom doit se terminer par []";
	msg_fr["is"] = " est de type ";
	msg_fr["warning"] = "ATTENTION :\n";

	// Messages anglais
	var msg_en = new Array();
	msg_en["toolong"] = "This address is too long";
	msg_en["invalid"] = "The email address you typed is invalid.";
	msg_en["mailrequired"] = "Please enter your email address.";
	msg_en["different"] = "The two email addresses don't match. Please try again."
	msg_en["confirm"] = "Please enter your confirmation email address.";
	msg_en["required1"] = 'The "';
	msg_en["required2"] = '" field is required.';
	msg_en["bracketsno"] = " is neither a checkbox nor select-multiple, so it's name musn't end with []";
	msg_en["bracketsyes"] = " and can have several values, it's name must end with []";
	msg_en["is"] = " is a ";
	msg_en["warning"] = "WARNING :\n";

	// Choix de la liste de messages en fonction de la langue du navigateur.
	var msg = (lang == 'fr') ? msg_fr : msg_en;
	this.msgs = msg;

	/*
	 * Cette fonction renvoie le texte écrit juste avant un élément donné.
	 * Elle parcourt l'arbre du document vers l'arrière jusqu'à trouver
	 * un textNode contenant autre chose que des espaces.
	 */
	function textePrecedent(element) {
		var ELEMENTNODE = 1;
		var TEXTNODE = 3;

		// Ce navigateur connaît-il le DOM ?
		if(!element.nodeType) return "no nodetype";

		// Tant qu'on n'a pas trouvé un textNode contenant autre chose que des espaces...
		while((element.nodeType != TEXTNODE) || (element.nodeValue.search(/^\s*$/) == 0)) {
			// On prend l'élément précédent.
			if(element.previousSibling)
				element = element.previousSibling;
			// S'il n'y a pas d'élément précédent on prend le précédent du parent.
			else if(element.parentNode && element.parentNode.previousSibling)
				element = element.parentNode.previousSibling;
			// On ne peut plus remonter, c'est qu'on n'a rien trouvé, on quitte.
			else return;

			// Si on arrive sur un élément qui a des descendants, on descend pour
			// trouver le texte le plus à droite.
			while((element.nodeType == ELEMENTNODE) && element.lastChild)
				element = element.lastChild;
		}
		return element.nodeValue;
	}

	/*
	 * Fonctions vérifiant la validité et la confirmation d'une adresse email.
	 */
	this.checkEmail = function(adr1) {
		adresse1 = adr1;
		var errMsg;
		if(adresse1 && !estVide(adresse1)) {
			if(adresse1.length > 100)
				errMsg = msg["toolong"];
			else {
				var atom = "[!#-'*+\\-\\/-9=?A-Z^-~]+";
				var regex_adresse =
					new RegExp("^"+atom+"(\\."+atom+")*@"+atom+"(\\."+atom+")*\\.[a-zA-Z]{2,4}$");
				if(adresse1.search(regex_adresse) != 0)
					errMsg = msg["invalid"];
			}
		}
		else if(champsObligatoires["email"])
			errMsg = msg["mailrequired"];
		if(errMsg)
			erreurs.push(errMsg);
		return errMsg;
	}

	function confirmeEmail(adresse2) {
		if(adresse2) {
			if(adresse1 && (adresse2 != adresse1))
				erreurs.push(msg["different"]);
		}
		else if(champsObligatoires["email2"])
			erreurs.push(msg["confirm"]);
	}


	/*
	 * Fonction vérifiant qu'un champ est rempli.
	 * On vérifie que la valeur n'est ni vide ni constituée uniquement d'espaces.
	 */
	function estVide(valeur) {
		return !valeur || (valeur.search(/^\s+$/) == 0);
// On vérifie que la valeur n'est ni vide ni constituée d'un seul caractère répété.
//		return (!valeur || (valeur.search(/^[\n\r]*(.)\1*[\n\r]*$/) >= 0));
	}

	function isSelect(node) {
		return (node.type.substr(0, 7) == 'select-');
	}

	/*
	 * Cette fonction vérifie le formulaire : syntaxe et confirmation de
	 * l'adresse email, présence des champs obligatoires.
	 */
	if(!f) f = document.forms[0];
	erreurs = [];
	if(!f) return;						// S'il n'y a pas de formulaire, on renvoie juste l'objet.
	adresse1 = '';
	var erreursFatales = [];

	// Vérification des champs obligatoires.
	var nom, node, texte;
	var radios = [];
	var multiples = [];

	for(var i = 0; i < f.elements.length; i++) {
		node = f.elements[i];
		nom = node.name;
		if(!nom) continue;

		// Ils doivent être précédés d'une étoile.
		texte = textePrecedent(node);
		if(texte && texte.search(/\*\s*$/) >= 0)
			champsObligatoires[nom] = 1;

		// Aide pour le webmaster : vérifications pour un transfert correct.
		if((node.type == 'checkbox') || (node.type == 'select-multiple')) {
			// Attention : substr(-2) renvoie la chaîne entière dans Internet Explorer !
			if(nom.substr(nom.length-2) != '[]') {
				if(!multiples[nom]) multiples[nom] = {t:node.type, n:0};
				multiples[nom].n++;
			}
		} else
			if(nom.substr(nom.length-2) == '[]')
				erreurs.push(nom+msg["bracketsno"]);
		if(nom == "email") this.checkEmail(node.value);
		else if(nom == "email2") confirmeEmail(node.value);
		else if(champsObligatoires[nom]) {
			if((node.type == 'radio') ||
			   (node.type == 'checkbox')) {
				if(radios[nom] == undefined) radios[nom] = false;
				radios[nom] |= node.checked;
			}
			else

			if(((node.type == 'select-one') && !node.selectedIndex) ||
			   ((node.type == 'select-multiple') && (node.selectedIndex < 0)) ||
				(!isSelect(node) && estVide(node.value)))				// node.value is not set for select elements with IE
				erreurs.push(msg["required1"]+nom+msg["required2"]);
		}
	}

	for(var r in radios)
		if(!radios[r]) erreurs.push(msg["required1"]+r+msg["required2"]);

	// Vérification d'une mauvaise conception du formulaire.
	for(var m in multiples)
		if(multiples[m].n > 1)
			erreursFatales.push(m+msg["is"]+multiples[m].t+msg["bracketsyes"]);
	// Affichage des erreurs et retour.
	if(erreursFatales.length) {
		alert(msg["warning"]+erreursFatales.join("\n"));
		return false;
	}
	if(erreurs.length) {
		alert(erreurs.join("\n"));
		return false;
	}
	return true;
}
