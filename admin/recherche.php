<?php
require_once 'header.php';
include ('./templates/links.html');
?>
<script type="text/javascript">
    function dataHtml(data){
        // Si l'ajout est réussi, afficher un message de réussite
        $('#cellule_'+ep_num).html(data);
        $('#cellule_'+ep_num).show('slow');
        setTimeout(function() {
            $('#cellule_'+ep_num).fadeOut('slow');
            $('#cellule_'+ep_num).html($('#cache_'+ep_num).html());
            $('#cache_'+ep_num).remove();
            setTimeout($('#cellule_'+ep_num).fadeIn(),750);

        },8000);
                
    }</script>
<div id="content">
    <h2>Rechercher</h2>
    <p> Exemple : pandora+4 -> l'ep 4 de Pandora</p>
    <div id="form">
        <input type="text" id="searchbox" name="searchbox" value="" size="255"/>
        <div id="buttonContainer">
            <a class="button" id="submitbutton" href="#"><span id="buttontext">Rechercher</span></a>
        </div>
    </div>
    <div id="resultsContainer"></div>

</div>
<div id="footer">
    <?php echo $close; ?>
</div>

</div>
</body>
</html>