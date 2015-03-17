$(document).ready(function(){
		$("a.reporter_lien").live('click',function(){
			if ($('#reporter').is(':hidden'))
			{
				$('#reporter').slideDown('slow');
			}
			else
			{
				$('#reporter').slideUp('slow');
			}
		});
		
	$("#Dead").click(function(){
		 var url = $("#formDead").serialize();
		// Utilisation d'Ajax / jQuery pour l'envoie
		 $.ajax({
			   type: "POST",
			   url: "suggestion.php",
			   data: url,
			   success: function html(data){
					// Si l'ajout est réussi, afficher un message de réussite
				$("#result").html(data); 
				$("#result").show("slow");
				setTimeout("$(\"#result\").slideUp(\"slow\")",3000);
			   }
			 });		
		// Nous retournons "false" au navigateur afin que la page ne soit pas actualisé
		return false;
	
	});

});
