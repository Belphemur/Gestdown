var ep_num;
var sortir=false;
var ajaxOptions = {
    type: "POST",
    url: "modifier_dl.php"
};
$(document).ready(function () {
    ajaxOptions.success = dataHtml;
    ajaxOptions.uploadProgress=function(event, position, total, percentComplete) {
        var percentVal = percentComplete + '%';
        $('#bar').width(percentVal)
        $('#percent').html(percentVal);
    };
    ajaxOptions.complete=function() {
        $('#progress').html("Redimensionnement de l'image et mise en ligne sur Imgur.com <br \> <img src='../images/ajax-loader.gif' alt='loader' />");
    }
    $("a.supprimer").live('click',function(){
        var num = $(this).attr("id").substr(5);

        $.ajax({
            type: "POST",
            url: "suppr_mod_download.php",
            data: "supprimer="+num,
            success: function html(data){
                // Si l'ajout est réussi, afficher un message de réussite
                $('#cellule_'+num).fadeOut("slow");
            }
        });
        return false;
    });
    $("a.modifier").live('click',function(){
        var num = $(this).attr("id").substr(6);
        var div = document.createElement("div");
        div.id="cache_"+num;   
        document.body.appendChild(div);
        $('#cache_'+num).hide();
        $('#cache_'+num).html($('#cellule_'+num).html());
        $('#cellule_'+num).fadeOut('slow');
        var data = "modifier="+num+'&ajax=1';
        if(sortir)
            data+='&sortir=1';
        $.ajax({
            type: "GET",
            url: "modifier_dl.php",
            data: data,
            success: function rephtml(data){
                // Si l'ajout est réussi, afficher un message de réussite
                $('#cellule_'+num).html(data);
                $('#cellule_'+num).fadeIn('slow');
            }
        });
        return false;
    });
    $("#modifDl").live('click',function(){
        $('#modif').hide();
        ep_num= $("#modifID").val();
        $("#progress").show();
        $("#Formdescription").ajaxSubmit(ajaxOptions);

        // !!! Important !!!
        // always return false to prevent standard browser submit and page navigation
        return false;

    });

});

