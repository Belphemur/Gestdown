<?php
if(isset($_GET['vue']))
{
    require_once('conf.php');
    $db->use_disk_cache = false;
    $db->cache_queries = false;
    include("login.php");
    login();
    $sql="SELECT c.nom, a.num ep,a.avancement ou, a.serie, a.id
		FROM avancement a 
		INNER JOIN categorie c 
		ON c.id=a.serie
		ORDER BY c.nom,a.num ASC";
    if($db->query($sql))
    {
        $results=$db->get_results();
        $serie="df";
        $i=1;
        foreach($results as $av)
        {
            if($serie!=$av['nom'])
            {
                $serie=$av['nom'];
                if($i==0)
                    echo '</table>
					            <br />
            <br />';
                echo '<h3>'.$serie.'</h3>';

                $i=0;
                ?>

<table width="450" border="1">
    <tr>
        <th scope="col">Numéro de l'ep</th>
        <th scope="col">Avancement</th>
        <th scope="col">Modifier</th>
        <th scope="col">Supprimer</th>
    </tr>


                    <?php
                }
                ?>

    <tr id="cellule_<?php echo $av['id'];?>">
        <th><?php echo $av['ep'];?></th>
        <th> en <span class="<?php echo $av['ou'];?>"><?php echo $av['ou'];?></span></th>
        <th>            <select name="mod_av" id="mod_av_<?php echo $av['id'];?>" size="1">
                <option value="" selected="selected"></option>
                <option value="Raw" >Raw</option>
                <option value="Vosta" >Vosta</option>
                <option value="Trad" >Trad</option>
                <option value="Time" >Time</option>
                <option value="Adapt" >Adapt</option>
                <option value="Edit" >Edit</option>
                <option value="Check" >Check</option>
                <option value="Enco" >Enco</option>
                <option value="Pause" >Pause</option>
            </select></th>
        <th> <a href="#" class="supprimer" id="supp_<?php echo $av['id'];?>">Supprimer</a>

    </tr>

                <?php

            }
            echo" </table>";
        }
        else
            echo "Aucun épisode en production <br />";

    }
    else if(isset($_POST['mod'],$_POST['id']))
    {
        require_once('conf.php');
        $db->use_disk_cache = false;
        $db->cache_queries = false;
        include("login.php");
        login();
        $mod=$_POST['mod'];
        $id=$_POST['id'];
        $time=time();
        $sql="UPDATE `avancement` SET `avancement` = '$mod', date ='$time' WHERE `avancement`.`id` =$id LIMIT 1 ";
        $db->query($sql);
        echo "Modification effectuée avec succès.";
    }
    else if(isset($_POST['serie'],$_POST['ep'],$_POST['avancement']))
    {
        require_once('conf.php');
        $db->use_disk_cache = false;
        $db->cache_queries = false;
        include("login.php");
        login();
        $time=time();
        foreach($_POST as $index => $valeur)
        {
            $$index = $db->real_escape(trim($valeur));
        }
        $sql="INSERT INTO `avancement` (`id`, `serie`, `num`, `avancement`, `date`) VALUES (NULL, '$serie' , '$ep', '$avancement', '$time')";
        $db->query($sql);
        echo "Ajout avec succès de l'épisode $ep en $avancement de la série ",$db->get_var("SELECT nom FROM categorie WHERE id=$serie"),'.';
    }
    else if (isset($_POST['id_supp']))
    {
        require_once('conf.php');
        $db->use_disk_cache = false;
        $db->cache_queries = false;
        include("login.php");
        login();
        $id=$_POST['id_supp'];
        $sql="DELETE FROM avancement WHERE id=$id";
        $db->query($sql);

        echo "Supprimé avec succès.";
    }
    else
    {
        require_once 'header.php';
        include ('./templates/links.html');

        $series="";
        $sql = "SELECT nom,id FROM categorie WHERE licencie!=1 AND finie!=1 AND nom!='Prob de lien' ORDER BY nom ASC";
        $donnees=$db->get_results($sql);
        foreach($donnees as $serie)
            $series.="<option value=\"{$serie['id']}\">{$serie['nom']}</option>";


        ?>
    <script type="text/javascript">
        function na_open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable)
        {
            toolbar_str = toolbar ? 'yes' : 'no';
            menubar_str = menubar ? 'yes' : 'no';
            statusbar_str = statusbar ? 'yes' : 'no';
            scrollbar_str = scrollbar ? 'yes' : 'no';
            resizable_str = resizable ? 'yes' : 'no';

            cookie_str = document.cookie;
            cookie_str.toString();

            pos_start  = cookie_str.indexOf(name);
            pos_end    = cookie_str.indexOf('=', pos_start);

            cookie_name = cookie_str.substring(pos_start, pos_end);

            pos_start  = cookie_str.indexOf(name);
            pos_start  = cookie_str.indexOf('=', pos_start);
            pos_end    = cookie_str.indexOf(';', pos_start);

            if (pos_end <= 0) pos_end = cookie_str.length;
            cookie_val = cookie_str.substring(pos_start + 1, pos_end);
            if (cookie_name == name && cookie_val  == "done")
                return;

            window.open(url, name, 'left='+left+',top='+top+',width='+width+',height='+height+',toolbar='+toolbar_str+',menubar='+menubar_str+',status='+statusbar_str+',scrollbars='+scrollbar_str+',resizable='+resizable_str);
        }
        function vue()
        {
            $('#result').hide('slow');

            $("#info").load("avancement.php?vue=1");
        }
        function supp(id)
        {
            $('#result').hide('slow');
            $('#cellule_'+id).fadeOut("slow");
        }
        $(document).ready(function () {
            $("#info").load("avancement.php?vue=1");
            $("#info select").live('change',function(){
                var num = $(this).attr("id").substr(7);
                var nouv=$("#mod_av_"+num+" option:selected").text();

                $.ajax({
                    type: "POST",
                    url: "avancement.php",
                    data: "id="+num+"&mod="+nouv,
                    success: function html(data){
                        // Si l'ajout est réussi, afficher un message de réussite
                        $("#result").text(data);
                        $("#result").show("slow");
                        setTimeout("vue()",1000);

                    }
                });
            });
            $("#info a.supprimer").live('click',function(){
                var num = $(this).attr("id").substr(5);

                $.ajax({
                    type: "POST",
                    url: "avancement.php",
                    data: "id_supp="+num,
                    success: function html(data){
                        // Si l'ajout est réussi, afficher un message de réussite
                        $("#result").text(data);
                        $("#result").show("slow");
                        setTimeout("supp("+num+")",1000);
                        if(confirm("Voulez-vous ajouter cet épisode ?"))
                            na_open_window('episode', './add_download.php', 0, 0, 950, 750, 0, 0, 0,1 , 1);
                    }
                });
                return false;
            });
            $("#ajouter").click(function(){
                var url = $("#form_av").serialize();
                // Utilisation d'Ajax / jQuery pour l'envoie
                var obj=document.getElementById("ep");
                var valeur=obj.value;

                if(!(!valeur || (valeur.search(/^\s+$/) == 0)))
                {
                    $.ajax({
                        type: "POST",
                        url: "avancement.php",
                        data: url,
                        success: function html(data){
                            // Si l'ajout est réussi, afficher un message de réussite
                            $("#result").text(data);
                            $("#result").show("slow");
                            setTimeout("vue()",4000);

                        }
                    });
                }
                else
                    alert("Champ Numéro de l'épisode VIDE");

                // Nous retournons "false" au navigateur afin que la page ne soit pas actualisé
                return false;

            });
        });</script>
    <style>
        label
        {
            color: #666;
            font-weight:bold;
            display: inline;
            float: left;
            width: 200px;
        }

        h3
        {
            color:#F00;
        }
        .Trad
        {color: #F00;}
        .Time
        {color: #F90;}
        .Adapt
        {color: #090;}
        .Edit
        {color: #C06;}
        .Check
        {color: #0080C0;}
        .Enco
        {color: #00F;}

    </style>
    <div id="content">
        <h2>Avancement</h2>
        <p>
        <form id="form_av" action="avancement.php" method="post">
            <label>Série :</label><select name="serie" size="1"><?php echo $series;?></select><br />
            <label>Numéro de l'épisode :</label>
            <input id="ep" name="ep" type="text" size="5" maxlength="3" /><br />
            <label>Avancement :</label>
            <select name="avancement" size="1">
                <option value="Raw" selected="selected" >Raw</option>
                <option value="Vosta" >Vosta</option>
                <option value="Trad" >Trad</option>
                <option value="Time" >Time</option>
                <option value="Adapt" >Adapt</option>
                <option value="Edit" >Edit</option>
                <option value="Check" >Check</option>
                <option value="Enco" >Enco</option>
            </select><br /><br />
            <input name="Ajouter" id="ajouter" type="submit" value="Ajouter" />
        </form>
        </p>
        <p>
        <div style="display:none;" id="result"></div>
        <div id="info"></div>
        </p>
    </div>
    <div id="footer">
            <?php echo $close; ?>
    </div>

</div>
</body>
</html>
    <?php
}
?>