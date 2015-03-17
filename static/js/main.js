function getHashFilter() {
    var hash = location.hash;
    // get filter=filterName
    var matches = location.hash.match( /filter=([^&]+)/i );
    var hashFilter = matches && matches[1];
    return hashFilter && decodeURIComponent( hashFilter );
}
var isIsotopeInit = false;

$(document).ready(function(){

    //mobile menu toggling
    $("#menu_icon").click(function(){
        $("header nav ul").toggleClass("show_menu");
        $("#menu_icon").toggleClass("close_menu");
        return false;
    });

    

    //Contact Page Map Centering
    var hw = $('header').width() + 50;
    var mw = $('#map').width();
    var wh = $(window).height();
    var ww = $(window).width();

    $('#map').css({
        "max-width" : mw,
        "height" : wh
    });

    if(ww>1100){
         $('#map').css({
            "margin-left" : hw
        });
    }

   



    //Tooltip
    $("a").mouseover(function(){

        var attr_title = $(this).attr("data-title");

        if( attr_title == undefined || attr_title == "") return false;
        
        $(this).after('<span class="tooltip"></span>');

        var tooltip = $(".tooltip");
        tooltip.append($(this).data('title'));

         
        var tipwidth = tooltip.outerWidth();
        var a_width = $(this).width();
        var a_hegiht = $(this).height() + 3 + 4;

        //if the tooltip width is smaller than the a/link/parent width
        if(tipwidth < a_width){
            tipwidth = a_width;
            $('.tooltip').outerWidth(tipwidth);
        }

        var tipwidth = '-' + (tipwidth - a_width)/2;
        $('.tooltip').css({
            'left' : tipwidth + 'px',
            'bottom' : a_hegiht + 'px'
        }).stop().animate({
            opacity : 1
        }, 200);
       

    });

    $("a").mouseout(function(){
        var tooltip = $(".tooltip");       
        tooltip.remove();
    });





    var isIsotopeInit = false;

    function onHashchange() {
        var hashFilter = getHashFilter();
        if ( !hashFilter && isIsotopeInit ) {
            return;
        }
        isIsotopeInit = true;
        // filter isotope
        $('#series').isotope({
            columnWidth: 200,
            itemSelector: '.work',
            filter: hashFilter
        });
        // set selected class on button
        if ( hashFilter ) {
            $("nav").find('.selected').removeClass('selected');
            $("nav").find('[data-filter="' + hashFilter + '"]').addClass('selected');
        }
    }

    $('nav').on( 'click', 'a', function(e) {
        e.preventDefault();
        var filterValue = $(this).attr('data-filter');
        if(filterValue) {
            $('#series').isotope({filter: filterValue});
            // set filter in hash
            location.hash = 'filter=' + encodeURIComponent( filterValue );
        }
    });

    $(window).on( 'hashchange', onHashchange );

    jQuery(window).load(function() {
        onHashchange();
    });
});





