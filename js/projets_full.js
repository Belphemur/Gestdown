var rotateSpeed = 500;
function showClass(classNum,id){
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
	
    var newLeft = classNum * 100 * -1;
    newLeft = newLeft + 400;
    newLeft = newLeft + "px";
	
    $("#class_rotator_classes").animate({
        left: newLeft
    }, rotateSpeed);
	
    $("#synopsis").load('./projets.php?id='+id,function(){
        $('#slideshow').cycle({
            delay:  2000,
            speed:  500,
            fit:	true
        });
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


$(document).ready(function(){
    $("#class_list a").click(function(){
        var classNum = $(this).attr("id").substr(10);
        var id = $(this).attr("rel");
        showClass(classNum,id);
    });
    $("#class_rotator_classes img").click(function(){
        var classNum = $(this).attr("id").substr(14);
        var id = $(this).attr("rel");
        showClass(classNum,id);
    });
    var cat =[];
    $('#class_rotator_classes img').each(function(i)
    {
        cat[i] = [$(this).attr('rel')];
    })
    var randomClassSelect = Math.floor(Math.random()*cat.length) +1;
    showClass(randomClassSelect+1,cat[randomClassSelect]);


});


