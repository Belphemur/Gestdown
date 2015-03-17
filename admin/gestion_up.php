<?php require_once 'header.php';
include ('./templates/linksUp.html'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        var refreshId = setInterval(function() {
            $.ajax({
                type: "GET",
                url: "cronMirror.php",
                data: "&randval="+ Math.random(),
                success: function html(data){
                    // Si l'ajout est réussi, afficher un message de réussite
                    //$("#myupload").html(data);
                    //$("#myupload").show("slow");
                }
            });
            //$("#myupload").load("http://www.gestdown.info/admin/autoUpload?randval="+ Math.random());
        }, 180000);
    });
</script>
<div id="content">
			<h2>Gestion des Uploads et Mirror</h2>
			<p>
			Vous avez ici la possibillité de gérer tous ce qui touche les uploads via le menu disponible à votre droite.
			</p>
                        <p style="color: red;">Nombre d'épisode à sortir : <span style="font-weight: bold;"><?php echo $db->get_var('SELECT count(d.id) FROM downloads d WHERE d.actif=0'); ?></span></p>
		</div>
<div id="footer">
		<?php echo $close; ?>
	</div>

</div>
</body>
</html>