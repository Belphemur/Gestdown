// args : string moncontroletexte, int nbcar, string moncontroledecompte 
// return : aucun 
// Affecte à certains évènements d'un textarea, le contrôle de la longueur de son contenu 
function LimiterTextArea(nom_controletexte, nbcar, nom_controledecompte) 
{ 
	var moncontroletexte = document.getElementById(nom_controletexte);

	var moncontroledecompte = document.getElementById(nom_controledecompte);

	if (moncontroletexte && moncontroledecompte)
	{

		moncontroletexte.onclick = function(){TextAreaEstRempli(moncontroletexte, nbcar, moncontroledecompte)}; 
		moncontroletexte.onblur = function(){TextAreaEstRempli(moncontroletexte, nbcar, moncontroledecompte)}; 
		moncontroletexte.onkeyup = function(){TextAreaEstRempli(moncontroletexte, nbcar, moncontroledecompte)}; 
		moncontroletexte.onkeypress = function(){TextAreaEstRempli(moncontroletexte, nbcar, moncontroledecompte)}; 

		// *** Affichage du nombre de caractères restant 
		if(moncontroledecompte.type)
			moncontroledecompte.value = NbCarRestant(moncontroletexte, nbcar);			// Pour un input de formulaire		
		else
			moncontroledecompte.innerHTML = NbCarRestant(moncontroletexte, nbcar);	// Pour un élément HTML	

	}
} 

// TextAreaEstRempli 
// args : textarea moncontroletexte, int nbcar, element_HTML moncontroledecompte 
// return : bool 
// Renvoie vrai si le nombre de caractères maximum du textarea n'est pas atteint 
function TextAreaEstRempli(moncontroletexte, nbcar, moncontroledecompte) 
{ 
	if (moncontroletexte) 
	{ 
		if (moncontroletexte.value.length <= nbcar) 
		{ 
			//alert("pas rempli"); 
			// mes actions ... 

			// *** Affichage du nombre de caractères restant
			if(moncontroledecompte.type)
				moncontroledecompte.value = NbCarRestant(moncontroletexte, nbcar);
			else
				moncontroledecompte.innerHTML = NbCarRestant(moncontroletexte, nbcar);

			return true; 
		} 
		else 
		{ 
			//alert("rempli"); 
			// mes actions ... 

			// Affichage du nombre de caractères restant 
			moncontroletexte.value = moncontroletexte.value.substr(0, nbcar); 

			// *** Affichage du nombre de caractères restant
			if(moncontroledecompte.type)
				moncontroledecompte.value = NbCarRestant(moncontroletexte, nbcar);
			else
				moncontroledecompte.innerHTML = NbCarRestant(moncontroletexte, nbcar);
				
			return false; 
		} 
	} 
} 

// NbCarRestant 
// args : textarea moncontroletexte, int nbcar 
// return : int 
// Renvoie le nombre de caractère à saisir 
function NbCarRestant(moncontroletexte, nbcar) 
{ 
	if (moncontroletexte.value.length)
		return new Number(nbcar - moncontroletexte.value.length); 
	else
		return new Number(nbcar);
} 