var rotateSpeed = 500;
function moveRotator(classNum){
    $("#class_list a.selected").removeClass("selected");
    $("#class_nav_"+classNum).addClass("selected");
    $("#class_rotator_wrapper .selected").animate({
        width: "80px",
        height: "103px",
        marginTop: "47px"
    }, rotateSpeed);
    $("#class_rotator_wrapper .selected").removeClass("selected");
	
    $("#class_rotator_"+classNum).animate({
        width: "153px",
        height: "197px",
        marginTop: "0px"
    }, rotateSpeed);
    $("#class_rotator_"+classNum).addClass("selected");
	
    var newLeft = classNum * 105 * -1;
    newLeft = newLeft + 500;
    newLeft = newLeft + "px";
	
    $("#class_rotator_classes").animate({
        left: newLeft
    }, rotateSpeed);
}
function showClass(classNum,id,nom){
    moveRotator(classNum);
    $("#synopsis").html('<div class="gauche"><div style="text-align:center;"><img src="http://images.gestdown.info/ajax-loader.gif" height="32" width="32" /><br /> Chargement.</div></div>');
    if(id==-1)
    {
        $("#synopsis").load('./ajax_index.php?id='+id+'&module=avancement');
        document.title = "Gestdown : Avancement des projets de la Ame no Tsuki";
    }
    else if(id!=0)
    {
        $("#synopsis").load('./ajax_index.php?id='+id+'&module=serie',function(){
            $("#episode_list a").removeAttr("href");
            document.title = "Gestdown : "+nom+" traduit par la Ame no Tsuki";
            window.fbAsyncInit = function() {
                FB.init({
                    appId: '120961717939204',
                    status: true,
                    cookie: true,
                    xfbml: true
                });
            };
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                '//connect.facebook.net/fr_FR/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());
        });


    }
    else
    {
        $("#synopsis").load('./ajax_index.php?id='+id+'&module=index');
        document.title = "Gestdown : Centralisation des liens de la Ame no Tsuki";
    }
}
function showEpisode(id)
{
    $("#episode_list a.selected").removeClass("selected");
    $("#"+id).addClass("selected");
    $("#episode_info").html('<div style="text-align:center;"><img src="templates/img/ajax-loader.gif" height="32" width="32" /><br /> Chargement.</div>');
    $("#prefetch").load('./ajax_index.php?id='+id+'&module=episode',function(){
        $('#markItUp').markItUp(mySettings);
        $("#episode_info").hide(100);
        setTimeout("$(\"#episode_info\").html($(\"#prefetch\").html());",101);
        setTimeout("$(\"#episode_info\").slideDown(1000)",102);
    });
		
    $('#serie').slideUp(1000);

}


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
function handleChange(event) {
    if(event.value!='/')
    {
        var info=event.path.split('/');
        if(info[1]=='serie')
        {
            showClass(info[2], info[3], info[4].replace(/_/g,' '));
        }
        else if(info[1]=='ep')
        {
            showEpisode(info[2]);
        }

    }

}

$(document).ready(function(){
    $("#class_list a").live('click',function(e){
        e.preventDefault();
        var classNum = $(this).attr("id").substr(10);
        var id = $(this).attr("rel");
        var name= $(this).attr("title");
        //showClass(classNum,id,name);
        SWFAddress.setValue('serie/'+classNum+'/'+id+'/'+name.replace(/ /g,'_'));
    });
    $("#class_rotator_classes img").live('click',function(e){
        e.preventDefault();
        var classNum = $(this).attr("id").substr(14);
        var id = $(this).attr("rel");
        var name= $(this).attr("title");
        //showClass(classNum,id,name);
        SWFAddress.setValue('serie/'+classNum+'/'+id+'/'+name.replace(/ /g,'_'));
    });
    $("#episode_list a").live('click',function(e){
        e.preventDefault();
        var id = $(this).attr("id");
        //showEpisode(id);
        SWFAddress.setValue('ep/'+id);
    });
		
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
    $("a.synopsis_link").live('click',function(){
        if ($('#synopsis_submit').is(':hidden'))
        {
            $('#synopsis_submit').slideDown('slow');
        }
        else
        {
            $('#synopsis_submit').slideUp('slow');
        }
    });
    $("#Dead").live('click',function(){
        //var url = $("#formDead").serialize();
        var url='id='+$('#idDead').val()+'&explication='+$('#explication').val()+'&quality='+$('#quality').val()+'&math_captcha='+$('#captchaDead').val()+'&mort=1';
        // Utilisation d'Ajax / jQuery pour l'envoie
        $.ajax({
            type: "POST",
            url: "suggestion.php",
            data: url,
            success: function html(data){
                // Si l'ajout est réussi, afficher un message de réussite
                $("#resultD").html(data);
                $("#resultD").show("slow");
                setTimeout("$(\"#resultD\").slideUp(\"slow\")",3000);
            }
        });
        // Nous retournons "false" au navigateur afin que la page ne soit pas actualisé
        return false;

    });
    $("#Synops").live('click',function(){
        //var url = $("#sugSynops").serialize();
        var url='id='+$('#idSug').val()+'&resume='+$('#markItUp').val()+'&auteur='+$('#auteurSug').val()+'&math_captcha='+$('#captchaSug').val()+'&screen='+$('#screen').val();
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
    $("#admin").live('click',function(){
        if ($('#admin_conn').is(':hidden'))
        {
            $('#admin_conn').slideDown('slow');
        }
        else
        {
            $('#admin_conn').slideUp('slow');
        }
    });
		
    $("#mdp_perdu").live('click',function(){
        if ($('#admin_mdp').is(':hidden'))
        {
            $('#admin_mdp').slideDown('slow');
        }
        else
        {
            $('#admin_mdp').slideUp('slow');
        }
    });
		
    $("#stats").live('click',function(){
        if ($('#statistiques').is(':hidden'))
        {
            $("#stats").text("| Masquer les Stats Totales|");
            $('#statistiques').slideDown('slow');
        }
        else
        {
            $("#stats").text("| Afficher les Stats Totales|");
            $('#statistiques').slideUp('slow');
        }
    });
    $("#d_stats").live('click',function(){
        if ($('#daily_stats').is(':hidden'))
        {
            $("#d_stats").text("| Masquer les Stats du Jour|");
            $('#daily_stats').slideDown('slow');
        }
        else
        {
            $("#d_stats").text("| Afficher les Stats du Jour|");
            $('#daily_stats').slideUp('slow');
        }
    });		
});
SWFAddress.addEventListener(SWFAddressEvent.CHANGE, handleChange);

