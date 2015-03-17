// JavaScript Document
function selectionStr(objet,balise,balise2)
{
    try {
        var textedebut;
        var textefin;
        var InfUrl;
        if (document.selection) {
            objet.focus();
            sel = document.selection.createRange();
            sel.text = balise + sel.text + balise2;
        } else {
            if(objet.selectionStart==objet.selectionEnd) {
                if (balise=="[img]" && balise2=="[/img]") {
                    InfUrl=prompt("Entrez l\'adresse URL de votre image SVP :", "");
                } else {
                    if (balise=="[url]" && balise2=="[/url]") {
                        InfUrl=prompt("Entrez l\'adresse URL :", "");
                    } else {
                        InfUrl="";
                    }
                }
                textedebut = objet.value.substring(0,objet.selectionStart);
                textefin = objet.value.substring(objet.selectionEnd, objet.value.length);
                objet.value = textedebut + balise + InfUrl + balise2 + textefin;
            } else {
                textedebut = objet.value.substring(0,objet.selectionStart);
                textefin = objet.value.substring(objet.selectionEnd, objet.value.length);
                var texteSelection = objet.value.substring(objet.selectionStart, objet.selectionEnd);
                objet.value = textedebut + balise + texteSelection + balise2 + textefin;
            }
        }
    } catch(e) {
        alert(e);
    };

}
function initBBcode(IdName,PrwName,largeur,hauteur,text,ep) {

    var InpBtn = '';
    var LabBtn = '';
    var DivBtn = '';
	
    var NamBtn = new Array('[b],[/b]','[u],[/u]','[i],[/i]','[s],[/s]','[left],[/left]','[center],[/center]','[right],[/right]','[justify],[/justify]','[quote],[/quote]',/*'[code],[/code]',*/'[list=circle]\r\n[*],\r\n[/list]','[list=1]\r\n[*],\r\n[/list]','[list=a]\r\n[*],\r\n[/list]'/*,'[img],[/img]','[url],[/url]','[mail],[/mail]'*/);
    var DesBtn = new Array('Gras','Souligné','Italique','Barré','Gauche','Centré','Droite','Justifié','Citation',/*'Affichez du code',*/'Liste à puce','Liste ordonnée','Liste alphabétique','Insérer une image','Insérer un lien','Insérer un mail');
    var ImgBtn = new Array('bold','underline','italic','strikethrough','alignleft','aligncenter','alignright','alignjustify','quote',/*'code',*/'bullist','numlist','alphalist'/*,'image','link','mail'*/);
	
    var DesCol = new Array('Couleur police'/*,'Couleur fond'*/);
    var NamCol = new Array('[color=',''/*,'[bgcolor='*/,'[/color]'/*,'[/bgcolor]'*/);
	
    var OptCol = new Array('Défaut', 'Rouge foncé', 'Rouge', 'Orange', 'Marron', 'Jaune', 'Vert', 'Olive', 'Cyan', 'Bleu', 'Bleu foncé', 'Indigo', 'Violet','Blanc','Noir');
    var HtmCol = new Array('','darkred', 'red', 'orange', 'brown', 'yellow', 'green', 'olive', 'cyan', 'blue', 'darkblue', 'indigo', 'violet', 'white','black');
	
    var DesSize = 'Taille';
    var NamSize = new Array('[size=','[/size]');
    var OptSize = new Array('Défaut','Très petit','Petit','Normal','Grand','Très grand','Personnaliser');
    var HtmSize = new Array('medium','x-small','small','medium','large','x-large','12px');
	
    var DesFont = 'Police';
    var NamFont = new Array('[font=','[/font]');
    var OptFont = new Array('Défaut','Arial','Verdana','Century Gothic','Comic Sans MS','Courier New','Jokerman','Kristen ITC','Lucida Console');
	
    // partie de la fonction qui permet d'ajouter les balises BBcode aux textes sélectionné, ou à l'emplacement du curseur
	
    //var Selection = 'try { if (document.selection) { objet.focus(); sel = document.selection.createRange(); sel.text = balise + sel.text + balise2; } else { if(objet.selectionStart==objet.selectionEnd) { if (balise=="[img]" && balise2=="[/img]") { var InfUrl=prompt("Entrez l\'adresse URL de votre image SVP :", ""); } else { if (balise=="[url]" && balise2=="[/url]") { var InfUrl=prompt("Entrez l\'adresse URL :", ""); } else { var InfUrl=""; } } var textedebut = objet.value.substring(0,objet.selectionStart); var textefin = objet.value.substring(objet.selectionEnd, objet.value.length); objet.value = textedebut + balise + InfUrl + balise2 + textefin; } else { var textedebut = objet.value.substring(0,objet.selectionStart); var textefin = objet.value.substring(objet.selectionEnd, objet.value.length); var texteSelection = objet.value.substring(objet.selectionStart, objet.selectionEnd); objet.value = textedebut + balise + texteSelection + balise2 + textefin; } } } catch(e) { alert(e); } ';
	
    // les 2 div qui vont bien => BBCode_ = Editeur ; Prev_ => Prévisu.
	
    document.write('<div id="BBCode_'+IdName+'" class="bbcode"></div><div id="Prev_'+IdName+'" class="bbcode"></div>');
	
	
    // Création du formulaire
	
    var html = '<div id="BBCodeBouton_'+IdName+'" class="BBCodeBouton"></div><div id="BBCodeArea_'+IdName+'" class="BBCodeArea"></div><div id="BBCodeSubmit_'+IdName+'" class="BBCodeSubmit"></div>';
	
    document.getElementById('BBCode_'+IdName).innerHTML = html;
    document.getElementById('BBCode_'+IdName).style.width = largeur+"px";
    document.getElementById('BBCode_'+IdName).align="left";
	
	
    document.getElementById('Prev_'+IdName).innerHTML = '<fieldset><legend>Prévisualisation :</legend></fieldset>';
    document.getElementById('Prev_'+IdName).style.display = "none";
    document.getElementById('Prev_'+IdName).align="left";
    document.getElementById('Prev_'+IdName).style.width = largeur+"px";
	
    document.getElementById('BBCodeArea_'+IdName).style.margin =  "0px 2.5px";
	
    document.getElementById('BBCodeSubmit_'+IdName).style.padding = "5px";
	
    // Ajout du textarea
	
    InpBtn = document.createElement( 'textarea' );
    InpBtn.id = IdName;
    InpBtn.name = IdName;
    InpBtn.value = text;
    InpBtn.style.height = hauteur+"px";
    InpBtn.style.width = largeur+"px";
    InpBtn.onkeyup = function() {
        preview(IdName);
    }
    InpBtn.style.width = largeur - 10 + "px";
    if(ep==0)
    {
        document.getElementById('BBCodeArea_'+IdName).innerHTML +='*';
    }
    document.getElementById('BBCodeArea_'+IdName).appendChild(InpBtn);
	

    // Bouton de prévisue
	
    InpBtn = document.createElement( 'input' );
    InpBtn.type = "button";
    InpBtn.id = "Prw"+IdName;
    InpBtn.name = "Prw"+IdName;
    InpBtn.value = PrwName;
	
    InpBtn.onclick = function() {
        document.getElementById('Prev_'+IdName).style.display = "block";
        var texte = preview(IdName);
        document.getElementById('Prev_'+IdName).innerHTML = '<fieldset><legend>Prévisualisation :</legend>'+texte+'</fieldset>';
    }
	
    document.getElementById('BBCodeSubmit_'+IdName).appendChild(InpBtn);
	
	
    // Le plus facile et plus simple, les balises de [b],[/b] à [url],[/url]
	
    DivBtn = document.createElement( 'div' );
    DivBtn.id = "SubBB_"+IdName;
	
    var i = 0;
	
    while (NamBtn[i]) {
			
        LabBtn = document.createElement( 'label' );
        LabBtn.title = DesBtn[i];
			
        InpBtn = document.createElement( 'input' );
        InpBtn.align = "middle";
        InpBtn.type = 'image';
        InpBtn.src = 'http://images.gestdown.info/bbcode/'+ImgBtn[i]+'.gif';
			
        var val = NamBtn[i].split(',');
        InpBtn.name = val[0];
        InpBtn.id = val[1];
			
        InpBtn.onclick = function() {
			
            var objet = eval("document.Form"+IdName+"."+IdName+";");
            var balise = this.name;
            var balise2 = this.id;
					
            //eval(Selection);
            selectionStr(objet,balise,balise2);
            preview(IdName);
        }
			
        LabBtn.appendChild(InpBtn);
        DivBtn.appendChild(LabBtn);
			
        i++;
			
    }
	
    document.getElementById('BBCodeBouton_'+IdName).appendChild(DivBtn);
	
    // on complique un peux, les Balises [color],[bgcolor]
	
    DivBtn = document.createElement( 'div' );
    DivBtn.id = "SelColBB_"+IdName;
	
    for (var i = 0;i < 1; i++) {
		
        LabBtn = document.createElement( 'label' );
        LabBtn.title = DesCol[i];
		
        LabBtn.innerHTML = "&nbsp;"+DesCol[i]+" : ";
        InpBtn = document.createElement( 'select' );
        InpBtn.id=NamCol[i];
        InpBtn.name=NamCol[i+2];
		
        var j = 0;
		
        while (OptCol[j]) {
		
            InpBtn.options[j] = new Option(OptCol[j],HtmCol[j]);
            if (i==0) {
                InpBtn.options[j].style.color = HtmCol[j];
            } else {
                InpBtn.options[j].style.background = HtmCol[j];
            }
			
            j++;
			
        }
		
        InpBtn.onchange =  function() {
					
            var objet = eval("document.Form"+IdName+"."+IdName+";");
            var balise = this.id+this.options[this.selectedIndex].value+']';
            var balise2 = this.name;
					
            eval(Selection);
            selectionStr(objet,balise,balise2);
            preview(IdName);
        }
		
        LabBtn.appendChild(InpBtn);
        DivBtn.appendChild(LabBtn);

    }
	
    document.getElementById('BBCodeBouton_'+IdName).appendChild(DivBtn);
	
    // La Balise [font] -- Type de Police --
	
    DivBtn = document.createElement( 'div' );
    DivBtn.id = "SelFontBB_"+IdName;
	
    LabBtn = document.createElement( 'label' );
    LabBtn.title = DesFont;
    LabBtn.innerHTML = "&nbsp;"+DesFont+" : ";
	
    InpBtn = document.createElement( 'select' );
    InpBtn.id=NamFont[0];
    InpBtn.name=NamFont[1];
	
    var i = 0;
	
    while (OptFont[i]) {
        InpBtn.options[i] = new Option(OptFont[i],OptFont[i]);
        i++;
    }
	
    InpBtn.onchange =  function() {
					 
        var objet = eval("document.Form"+IdName+"."+IdName+";");
        var balise = this.id+this.options[this.selectedIndex].value+']';
        var balise2 = this.name;
					
        eval(Selection);
        selectionStr(objet,balise,balise2);
        preview(IdName);
    }
		
    LabBtn.appendChild(InpBtn);
    DivBtn.appendChild(LabBtn);
	
    document.getElementById('BBCodeBouton_'+IdName).appendChild(DivBtn);
	
    // La Balise [size] -- Taille de la police de caractère --
	
    DivBtn = document.createElement( 'div' );
    DivBtn.id = "SelSizeBB_"+IdName;
	
    LabBtn = document.createElement( 'label' );
    LabBtn.title = DesSize;
    LabBtn.innerHTML = "&nbsp;"+DesSize+" : ";
	
    InpBtn = document.createElement( 'select' );
    InpBtn.id=NamSize[0];
    InpBtn.name=NamSize[1];
	
    var i = 0;
	
    while (OptSize[i]) {
        InpBtn.options[i] = new Option(OptSize[i],HtmSize[i]);

        i++;
    }
	
    InpBtn.onchange =  function() {
					
        var objet = eval("document.Form"+IdName+"."+IdName+";");
        var balise = this.id+this.options[this.selectedIndex].value+']';
        var balise2 = this.name;
					
        eval(Selection);
        selectionStr(objet,balise,balise2);
        preview(IdName);
    }
		
    LabBtn.appendChild(InpBtn);
    DivBtn.appendChild(LabBtn);
	
    document.getElementById('BBCodeBouton_'+IdName).appendChild(DivBtn);
	

	
}
function preview(Name) {
	
    //	if (document.getElementById('Prev_'+Name).style.display == "none") return false;
	
    var q1 = "<table align=center border=0 cellpadding=3 cellspacing=1 width=90%><tbody><tr><td><div align=\"left\" style=\"font-size : 11px; color: #000000;\"><b>";
    var q2 = "</b></div></td></tr><tr><td align=\"left\" style=\"font-family: Arial; font-size: 11px; color: #444444; background-color: #FAFAFA; border: #D1D7DC; border-style: solid; border-width: 1px;\">";
    var q3 =  "</td></tr></tbody></table>";
	
    var texte = document.getElementById(Name).value;
	
    texte=texte.replace(/</g, '&lt;');
    texte=texte.replace(/>/g, '&gt;');
		
    texte=texte.replace(/\r\n|\r|\n/g, '<br />');
    texte=texte.replace(/\[(b|u|s|i|\/b|\/u|\/s|\/i)]/g,'<$1>');
	
    texte=texte.replace(/\[color=([^\[]*)\]/mig, '<span style=\'color:$1;\'>');
    texte=texte.replace(/\[bgcolor=([^\[]*)\]/mig, '<span style=\'background-color:$1;\'>');
    texte=texte.replace(/\[font=([^\[]*)\]/mig, '<span style=\'font-family:$1;\'>');
    texte=texte.replace(/\[size=([^\[]*)\]/mig, '<span style=\'font-size:$1;\'>');
    texte=texte.replace(/\[\/(color|bgcolor|font|size)\]/gi,'</span>');
	
    texte=texte.replace(/\[center\]/mig, '<div style=\'text-align:center;\'>');
    texte=texte.replace(/\[\/center]/gi, '</div>');
	
    texte=texte.replace(/\[right\]/mig, '<div style=\'text-align:right;\'>');
    texte=texte.replace(/\[\/right]/gi, '</div>');
	
    texte=texte.replace(/\[left\]/mig, '<div style=\'text-align:left;\'>');
    texte=texte.replace(/\[\/left]/gi, '</div>');
	
    texte=texte.replace(/\[justify\]/mig, '<div style=\'text-align:justify;\'>');
    texte=texte.replace(/\[\/justify]/gi, '</div>');
	
    texte=texte.replace(/\[code]/gi, '<div align="left" style="color:#000000;font-weight:bold; font-size: 11px;">Code :</div><div style="color:#333333;background-color:#F0F0F0;" align="left"><code lang="fr">');
    texte=texte.replace(/\[\/code]/gi, '</code></div>');
	
    texte=texte.replace(/\[quote]/gi, q1+'Citation:'+q2);
    texte=texte.replace(/\[quote=([^\[]*)\]/gi, q1+'$1 a écrit :'+q2);
    texte=texte.replace(/\[\/quote]/gi, q3);
	
    texte=texte.replace(/\[url]([^\]]*)\[\/url]/mig,'<a href="$1" target="_blank">$1</a>');
    texte=texte.replace(/\[url=([^\[]*)\]([^\]]*)\[\/url\]/mig, '<a href=\'$1\' target=\'_blank\'>$2</a>');
    texte=texte.replace(/\[mail]([^\[]*)\[\/mail]/mig,'<a href="mailto:$1" target="_blank">$1</a>');
    texte=texte.replace(/\[mail=([^\[]*)\]([^\]]*)\[\/mail\]/mig, '<a href=\'mailto:$1\' target=\'_blank\'>$2</a>');
    texte=texte.replace(/\[img]([^\]]*)\[\/img]/mig,'<img src="$1" border="0" />');

    texte=texte.replace(/\[list=([^\]]*)\](.+)\[\/list]/mig, '<ul type="$1">$2</ul>');
    texte=texte.replace(/\[\*]([^\[]+)(?=(\[\*])|(<\/ul>))/mig, '<li>$1</li>');

    texte=texte.replace(/\<br \/><\/ul>/gi, '</ul>');
    texte=texte.replace(/<\/ul><br \/>/gi, '</ul>');

    texte=texte.replace(/<div([^\>]*)><br \/>/gi, '<div $1>');
    texte=texte.replace(/<\/div><br \/>/gi, '</div>');
	
    texte=texte.replace(/<span([^\>]*)><br \/>/gi, '<span $1>');
    texte=texte.replace(/(<\/span><br \/>|<br \/><\/span><br \/>|<br \/><\/span>)/gi, '</span>');
	
    texte=texte.replace(/<td([^\>]*)><br \/>/gi, '<td $1>');
    texte=texte.replace(/<\/table><br \/>/gi, '</table>');
	
    texte=texte.replace(/<code([^\>]*)><br \/>/gi, '<code $1>');
    texte=texte.replace(/(<\/code><br \/>|<br \/><\/code>|<br \/><\/code><br \/>)/gi, '</code>');

    document.getElementById('Prev_'+Name).innerHTML = '<fieldset><legend>Prévisualisation :</legend>'+texte+'</fieldset>';
    return (texte);
}