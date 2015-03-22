$(document).ready(function(){
		$("a.reporter_lien").click(function(){
			if ($('#reporter').is(':hidden'))
			{
				$('#reporter').slideDown('slow');
			}
			else
			{
				$('#reporter').slideUp('slow');
			}
		});
		
	$("#Dead").click(function(e){
		 var url = $("#formDead").serialize();
		e.preventDefault();
		// Utilisation d'Ajax / jQuery pour l'envoie
		 $.ajax({
			   type: "POST",
			   url: "suggestion.php",
			   data: url,
			   dataType: "text",
			   success: function html(data){
					// Si l'ajout est réussi, afficher un message de réussite
				$("#reporter").text(data);
				setTimeout(function() {
					$('#reporter').slideUp('slow');
				},3000);
			   }
			 });		
		// Nous retournons "false" au navigateur afin que la page ne soit pas actualisé
	
	});

});
