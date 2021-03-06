// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'~/sets/bbcode/parser/markitup.bbcode-parser.php', // path to your BBCode parser
	markupSet: [
		  {	name:'Colors', 
	  		className:'colors', 
	 		 openWith:'[color=[![Couleur]!]]', 
	 	 	closeWith:'[/color]', 
		  dropMenu: [
			  {name:'Jaune',	openWith:'[color=yellow]', 	closeWith:'[/color]', className:"col1-1" },
			  {name:'Orange',	openWith:'[color=orange]', 	closeWith:'[/color]', className:"col1-2" },
			  {name:'Rouge', 	openWith:'[color=red]', 	closeWith:'[/color]', className:"col1-3" },
			  
			  {name:'Bleu', 	openWith:'[color=blue]', 	closeWith:'[/color]', className:"col2-1" },
			  {name:'Mauve', 	openWith:'[color=purple]', 	closeWith:'[/color]', className:"col2-2" },
			  {name:'Vert', 	openWith:'[color=green]', 	closeWith:'[/color]', className:"col2-3" },
			  
			  {name:'Blanc', 	openWith:'[color=white]', 	closeWith:'[/color]', className:"col3-1" },
			  {name:'Gris', 	openWith:'[color=gray]', 	closeWith:'[/color]', className:"col3-2" },
			  {name:'Noir',		openWith:'[color=black]', 	closeWith:'[/color]', className:"col3-3" },
			  
			  {name:'Skyblue',		openWith:'[color=skyblue]', 	closeWith:'[/color]', className:"col4-1" },
			  {name:'Royalblue',	openWith:'[color=royalblue]', 	closeWith:'[/color]', className:"col4-2" },
			  {name:'Orangered',	openWith:'[color=orangered]', 	closeWith:'[/color]', className:"col4-3" },
			  
			  {name:'Crimson', 	openWith:'[color=crimson]', closeWith:'[/color]', className:"col5-1" },
			  {name:'Darkred', 	openWith:'[color=darkred]', closeWith:'[/color]', className:"col5-2" },
			  {name:'Teal', 	openWith:'[color=teal]', 	closeWith:'[/color]', className:"col5-3" },
			  
			  {name:'sienna', 	openWith:'[color=sienna]', 	closeWith:'[/color]', className:"col6-1" },
			  {name:'coral', 	openWith:'[color=coral]', 	closeWith:'[/color]', className:"col6-2" },
			  {name:'deeppink', openWith:'[color=deeppink]',closeWith:'[/color]', className:"col6-3" }
		  ]
		},
		{separator:'---------------' },
		{name:'Gras', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Italique', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Souligner', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'Image', key:'P', replaceWith:'[img][![L\'Url de votre Image]!][/img]'},
		{name:'Lien', key:'L', openWith:'[url=[![L\'Url de votre lien]!]]', closeWith:'[/url]', placeHolder:'[![Le texte de votre lien]!]'},
		{name:'Email', key:'E', openWith:'[email][![L\'adresse mail]!]]', closeWith:'[/email]'},
		{separator:'---------------' },
		{name:'Taille', key:'S', openWith:'[size=[![La taille de votre texte (entre 1 et 5)]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Grand', openWith:'[size=5]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=3]', closeWith:'[/size]' },
			{name:'Petit', openWith:'[size=1]', closeWith:'[/size]' }
		]},
		{separator:'---------------' },
		{name:'Liste', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Liste numérique', openWith:'[list=[![Starting number]!]]\n', closeWith:'\n[/list]'}, 
		{name:'List item', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Citation', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Clean', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } },
		{name:'Prévisualiser', className:"preview", call:'preview' }
		
	]
}