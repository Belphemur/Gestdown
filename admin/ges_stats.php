<?php
require_once 'header.php';
unset($db);
$db = ezDB::getInstance();
$db->connect($sql_serveur, $sql_login, $sql_pass, $sql_bdd);
$db->query("SET NAMES 'utf8'");
?>
<script type="text/javascript">
    function DecomposeDate(LeParam1){

        // Sépare les jours, les mois et les années dans une date de type "22/05/1981"
        // Renvoye le tout dans un tableau de taille 3

        LeRetour = new Array(3);
        LeJour="";
        LeMois="";
        LeAnnee="";

        // Extraction du jour
        i=0;
        while((LeParam1.charAt(i)!="/")&&(i<10)){
            LeJour+=LeParam1.charAt(i);
            i++;
        }
        if(LeJour.charAt(0)=="0"){
            LeJour=LeJour.charAt(1);
        }
        LeParam1=LeParam1.substring(i+1,LeParam1.length);

        // Extraction du mois
        i=0;
        while((LeParam1.charAt(i)!="/")&&(i<10)){
            LeMois+=LeParam1.charAt(i);
            i++;
        }
        if(LeMois.charAt(0)=="0"){
            LeMois=LeMois.charAt(1);
        }
        LeParam1=LeParam1.substring(i+1,LeParam1.length);


        // Extraction de l'année
        LeAnnee=LeParam1;
        LeRetour[0]=LeJour;
        LeRetour[1]=LeMois;
        LeRetour[2]=LeAnnee;
        return LeRetour;
    }

    function sendDate()
    {
        var frm=document.forms["form_date"];
        var date =  frm.elements["date"].value;
        var radio=frm.type;
        var type;
        for (var i=0; i<radio.length;i++)
        {
            if (radio[i].checked)
            {
                type = radio[i].value;
            }
        }
        $("#stats").load('./ajax_stats.php?date='+date+'&type='+type);
    }

    function compare(daily)
    {
        var frm=document.forms["form_date"];
        var date =  frm.elements["date"].value;
        var date_e = frm.elements["date_e"].value;
        date_first=DecomposeDate(date);
        date_end=DecomposeDate(date_e);
        date_first=new Date(date_first[2],date_first[0],date_first[1]);
        date_end=new Date(date_end[2],date_end[0],date_end[1]);
	
        if(date_first==date_end)
            alert('Les dates de comparaison doivent être différente');
        else if(date_first>date_end)
            alert('La date de début doit être avant la date de fin');
        else
        {
            if(daily)
                $("#stats").load('./ajax_stats.php?date='+date+'&date_end='+date_e+'&type=d');
            else
                $("#stats").load('./ajax_stats.php?date='+date+'&date_end='+date_e+'&type=t');
        }
    }
    $(document).ready(function(){
        $("#jours").live('click',function(){
            if ($('#compare').is(':hidden'))
            {
                $("#choice_date").text("Date de début :");
                $('#compare').slideDown('slow');
                $('#date_choice_first').slideDown('slow');
                $('#afficher').slideUp('slow');
            }
            else
            {
                $('#compare').slideUp('slow');
            }
        });
	
        $("#date").live('click',function(){
            if ($('#afficher').is(':hidden'))
            {
                $("#choice_date").text("Choisissez la date :");
                $('#date_choice_first').slideDown('slow');
                $('#afficher').slideDown('slow');
                $('#compare').slideUp('slow');
            }
            else
            {
                $('#afficher').slideUp('slow');
            }
        });
        $('#datepicker_end').datepicker({minDate: new Date(2009,9, 23), maxDate: '+0D'});
        $('#datepicker').datepicker({minDate: new Date(2009,9, 23), maxDate: '+0D'});

    });



</script>

<a id="jours" href="#">Stats entre 2 dates</a> <br /><br  />
<a id="date" href="#">Avoir les stats d'un jour particulier</a><br />
<label style="display:none" id="format">yy-mm-dd</label>
<div style="display:none" id="date_choice_first">
    <form id="form_date">

        <label id="choice_date">Choisissez la date</label>
        <input type="text" id="datepicker" name="date">
        </div>
        <div style="display:none" id="afficher">
            <label>Type : </label>
            <p>
                <label>
                    <input  type="radio" name="type" value="d" id="type_0" />
                    Journalière</label>
                <br />
                <label>
                    <input checked="checked" type="radio" name="type" value="t" id="type_1" />
                    Générale</label>
                <br />
            </p>
            <input name="Afficher"  type="button" onClick="sendDate()" value="Afficher" />
        </div>
        <div style="display:none" id="compare">
            <label>Date de fin :</label>
            <input type="text" id="datepicker_end" name="date_e">
            <input name="Comparer"  type="button" onClick="compare(false)" value="Générale" />&nbsp;&nbsp;
            <input name="Comparer"  type="button" onClick="compare(true)" value="Diff. Jour." />
        </div>
    </form>

</div>
<div id="content">
    <div id="stats">
        <?php
        $stat = new Stats(time(), $db);
        echo $stat->total_display();
        ?>
    </div>
</div>
<div id="footer">
<?php echo $close; ?> 
</div>
</body>
</html>