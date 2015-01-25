$(window).resize(function() {
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var ll = $('#lateral_left');

    var width_lateral_left = (w.width()-p.width())/2;
    ll.width(width_lateral_left);

    if(w.height() > cp.outerHeight(true))
        height = w.outerHeight(true);
    else
        height = cp.outerHeight(true);

    ll.height(height);
    $("#page_lateral_profil").height(height);
});
$(window).load(function(){
    var height = 0;
    var w = $(window);
    var p = $('#page');
    var cp = $('#page_contenu');
    var ll = $('#lateral_left');

    var width_lateral_left = (w.width()-p.width())/2;
    ll.width(width_lateral_left);

    if(w.height() > cp.height())
        height = w.height();
    else
        height = cp.height();

    ll.height(height);
    $("#page_lateral_profil").height(height);

    $('textarea').elastic();
});
