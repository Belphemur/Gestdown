/**
 * Created by Antoine on 18/03/2015.
 */
function getHashFilter() {
    var hash = location.hash;
    // get filter=filterName
    var matches = location.hash.match( /filter=([^&]+)/i );
    var hashFilter = matches && matches[1];
    return hashFilter && decodeURIComponent( hashFilter );
}
var isIsotopeInit = false;





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

$(window).on( 'hashchange', onHashchange );

$(window).load(function() {
    onHashchange();
});

$(document).ready(function() {
    $('nav').on('click', 'a', function (e) {
        e.preventDefault();
        var filterValue = $(this).attr('data-filter');
        if (filterValue) {
            $('#series').isotope({filter: filterValue});
            // set filter in hash
            location.hash = 'filter=' + encodeURIComponent(filterValue);
        }
    });
});