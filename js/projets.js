var rotateSpeed=500;function showClass(a,c){$("#class_list a.selected").removeClass("selected");$("#class_nav_"+a).addClass("selected");$("#class_rotator_wrapper .selected").animate({width:"80px",height:"103px",marginTop:"47px"},rotateSpeed);$("#class_rotator_wrapper .selected").removeClass("selected");$("#class_rotator_"+a).animate({width:"153px",height:"197px",marginTop:"0px"},rotateSpeed);$("#class_rotator_"+a).addClass("selected");var b=a*100*-1;b=b+400;b=b+"px";$("#class_rotator_classes").animate({left:b},rotateSpeed);$("#synopsis").load("./projets.php?id="+c,function(){$("#slideshow").cycle({delay:2000,speed:1000,fit:true})})}$(document).ready(function(){$("#class_list a").click(function(){var c=$(this).attr("id").substr(10);var d=$(this).attr("rel");showClass(c,d)});$("#class_rotator_classes img").click(function(){var c=$(this).attr("id").substr(14);var d=$(this).attr("rel");showClass(c,d)});var a=[];$("#class_rotator_classes img").each(function(c){a[c]=[$(this).attr("rel")]});var b=Math.floor(Math.random()*a.length)+1;showClass(b+1,a[b])});